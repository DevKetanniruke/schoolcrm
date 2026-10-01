<?php

namespace App\Livewire\Profile;

use App\Core\Services\AuditLogger;
use App\Rules\PhoneRule;
use App\Rules\SanitizedTextRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProfile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public $avatar = null;

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function updateProfileInformation(AuditLogger $auditLogger)
    {
        $user = auth()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', new PhoneRule],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar_path && Storage::disk('uploads')->exists($user->avatar_path)) {
                Storage::disk('uploads')->delete($user->avatar_path);
            }
            $avatarPath = $this->avatar->store('avatars', 'uploads');
            $user->avatar_path = $avatarPath;
        }

        $user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ])->save();

        $auditLogger->log(
            action: 'user.profile_updated',
            payload: ['user_id' => $user->id, 'name' => $this->name, 'email' => $this->email]
        );

        $this->avatar = null;
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Profile Updated',
            'message' => 'Your profile information has been successfully saved.',
        ]);
    }

    public function updatePassword(AuditLogger $auditLogger)
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($this->new_password),
        ])->save();

        $auditLogger->log(
            action: 'user.password_changed',
            payload: ['user_id' => $user->id]
        );

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Password Changed',
            'message' => 'Your security password has been updated successfully.',
        ]);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        return view('livewire.profile.edit-profile', [
            'user' => auth()->user(),
        ]);
    }
}
