<?php

namespace App\Livewire\Communication;

use App\Core\Communication\Services\CommunicationManager;
use App\Models\AdmissionLead;
use App\Models\Communication;
use App\Models\CommunicationTemplate;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Composer extends Component
{
    use AuthorizesRequests;

    public string $title = '';
    public string $channel = 'email';
    public string $recipient_type = 'parent_group';

    // Target filters
    public ?int $target_class_id = null;
    public ?int $target_user_id = null;
    public ?int $target_student_id = null;
    public ?int $target_parent_id = null;
    public ?int $target_lead_id = null;

    // Template & Content
    public ?int $template_id = null;
    public string $subject = '';
    public string $message_body = '';

    // Schedule
    public bool $is_scheduled = false;
    public string $scheduled_at = '';

    public array $availableVariables = [
        'student_name' => 'Student Full Name',
        'parent_name' => 'Parent/Guardian Name',
        'school_name' => 'School Name',
        'name' => 'Recipient Name',
    ];

    public function updatedTemplateId($val): void
    {
        if (! $val) {
            return;
        }

        $template = CommunicationTemplate::find($val);
        if ($template) {
            $this->subject = $template->subject ?? '';
            $this->message_body = $template->content_body;
            $this->channel = $template->channel;
        }
    }

    public function insertVariable(string $varName): void
    {
        $this->message_body .= ' {' . $varName . '} ';
    }

    public function dispatchBroadcast()
    {
        $this->authorize('create', Communication::class);

        $this->validate([
            'title' => 'required|string|max:150',
            'channel' => 'required|in:in_app,email,sms,whatsapp,multi_channel',
            'recipient_type' => 'required|in:individual,class,parent_group,staff_group,school_wide,admission_leads',
            'subject' => 'nullable|string|max:200',
            'message_body' => 'required|string|min:5',
            'scheduled_at' => $this->is_scheduled ? 'required|date|after:now' : 'nullable',
        ]);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $targetFilters = [];
        if ($this->recipient_type === 'class') {
            $targetFilters['class_id'] = $this->target_class_id;
        } elseif ($this->recipient_type === 'individual') {
            $targetFilters['user_id'] = $this->target_user_id;
            $targetFilters['student_id'] = $this->target_student_id;
            $targetFilters['parent_id'] = $this->target_parent_id;
            $targetFilters['lead_id'] = $this->target_lead_id;
        }

        $communication = Communication::create([
            'school_id' => $schoolId,
            'sender_id' => Auth::id(),
            'template_id' => $this->template_id,
            'title' => $this->title,
            'channel' => $this->channel,
            'recipient_type' => $this->recipient_type,
            'target_filters_json' => $targetFilters,
            'subject' => $this->subject,
            'message_body' => $this->message_body,
            'scheduled_at' => $this->is_scheduled ? $this->scheduled_at : null,
            'status' => $this->is_scheduled ? 'queued' : 'processing',
        ]);

        if (! $this->is_scheduled) {
            $manager = new CommunicationManager();
            $manager->dispatchCommunication($communication);

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Broadcast Dispatched',
                'message' => "Communication broadcast '{$communication->title}' sent to recipients.",
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Broadcast Scheduled',
                'message' => "Communication scheduled for release on {$communication->scheduled_at->format('M d, Y h:i A')}.",
            ]);
        }

        return redirect()->route('communication.logs');
    }

    public function render()
    {
        $this->authorize('viewAny', Communication::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $templates = CommunicationTemplate::where('school_id', $schoolId)->where('status', 'active')->get();
        $classes = SchoolClass::orderBy('year_level')->get();
        $students = Student::where('school_id', $schoolId)->limit(50)->get();
        $parents = ParentProfile::where('school_id', $schoolId)->limit(50)->get();
        $users = User::where('school_id', $schoolId)->limit(50)->get();
        $leads = AdmissionLead::where('school_id', $schoolId)->limit(50)->get();

        return view('livewire.communication.composer', [
            'templates' => $templates,
            'classes' => $classes,
            'students' => $students,
            'parents' => $parents,
            'users' => $users,
            'leads' => $leads,
        ])->layout('components.layouts.admin', ['title' => 'Multi-Channel Communication Composer']);
    }
}
