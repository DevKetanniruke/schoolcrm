<?php

namespace App\Livewire\Communication;

use App\Models\CommunicationTemplate;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Templates extends Component
{
    use AuthorizesRequests;

    public string $search = '';
    public string $category = '';
    public string $channel = '';

    // Modal Form
    public bool $showModal = false;
    public ?int $editingTemplateId = null;
    public string $title = '';
    public string $code = '';
    public string $tpl_channel = 'email';
    public string $tpl_category = 'general';
    public string $subject = '';
    public string $content_body = '';
    public string $status = 'active';

    public function createTemplate(): void
    {
        $this->authorize('create', CommunicationTemplate::class);
        $this->resetForm();
        $this->showModal = true;
    }

    public function editTemplate(int $id): void
    {
        $tpl = CommunicationTemplate::findOrFail($id);
        $this->authorize('update', $tpl);

        $this->editingTemplateId = $tpl->id;
        $this->title = $tpl->title;
        $this->code = $tpl->code;
        $this->tpl_channel = $tpl->channel;
        $this->tpl_category = $tpl->category;
        $this->subject = $tpl->subject ?? '';
        $this->content_body = $tpl->content_body;
        $this->status = $tpl->status;

        $this->showModal = true;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingTemplateId',
            'title',
            'code',
            'tpl_channel',
            'tpl_category',
            'subject',
            'content_body',
            'status',
        ]);
        $this->tpl_channel = 'email';
        $this->tpl_category = 'general';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function saveTemplate(): void
    {
        $this->authorize('create', CommunicationTemplate::class);

        $this->validate([
            'title' => 'required|string|max:150',
            'code' => 'required|string|max:50',
            'tpl_channel' => 'required|in:in_app,email,sms,whatsapp,multi_channel',
            'tpl_category' => 'required|in:academic,attendance,fees,events,admissions,emergency,general',
            'subject' => 'nullable|string|max:200',
            'content_body' => 'required|string|min:5',
            'status' => 'required|in:active,draft,archived',
        ]);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        CommunicationTemplate::updateOrCreate(
            ['id' => $this->editingTemplateId],
            [
                'school_id' => $schoolId,
                'title' => $this->title,
                'code' => strtoupper($this->code),
                'channel' => $this->tpl_channel,
                'category' => $this->tpl_category,
                'subject' => $this->subject,
                'content_body' => $this->content_body,
                'variables_json' => ['student_name', 'parent_name', 'school_name'],
                'status' => $this->status,
                'created_by' => Auth::id(),
            ]
        );

        $this->showModal = false;
        $this->resetForm();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Template Saved',
            'message' => 'Communication message template saved successfully.',
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', CommunicationTemplate::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $query = CommunicationTemplate::where('school_id', $schoolId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category !== '') {
            $query->where('category', $this->category);
        }

        if ($this->channel !== '') {
            $query->where('channel', $this->channel);
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.communication.templates', [
            'templates' => $templates,
        ])->layout('components.layouts.admin', ['title' => 'Message Templates Manager']);
    }
}
