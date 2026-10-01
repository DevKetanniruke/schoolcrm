<div>
    <!-- Page Header & Title -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <span>Account</span>
            <span>/</span>
            <span class="text-indigo-600 dark:text-indigo-400 font-semibold">My Profile</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Account Settings & Security</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage your personal profile details, photo, and access security credentials.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Summary Card (Left Column) -->
        <div class="space-y-6">
            <x-ui.card>
                <div class="text-center">
                    <!-- Avatar Photo Display -->
                    <div class="relative inline-block mb-4">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-indigo-500/30 mx-auto" />
                        @elseif ($user->avatar_path)
                            <img src="{{ asset('storage/uploads/' . $user->avatar_path) }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-indigo-500/30 mx-auto" />
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-extrabold text-2xl flex items-center justify-center ring-4 ring-indigo-500/30 mx-auto">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>

                    <!-- User Roles Badges -->
                    <div class="flex flex-wrap justify-center gap-1.5 mt-3">
                        @forelse ($user->roles as $role)
                            <x-ui.badge variant="primary" size="sm">{{ $role->name }}</x-ui.badge>
                        @empty
                            <x-ui.badge variant="neutral" size="sm">No Assigned Role</x-ui.badge>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 my-4"></div>

                    <!-- Telemetry Metadata -->
                    <div class="space-y-2 text-xs text-left">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Account Status</span>
                            <x-ui.badge :variant="$user->status === 'active' ? 'success' : 'danger'" size="sm">{{ ucfirst($user->status ?? 'active') }}</x-ui.badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">School Tenant</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->school->name ?? 'Global' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Last Login</span>
                            <span class="font-mono text-slate-600 dark:text-slate-400 text-[11px]">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Edit Profile & Security Forms (Right Column) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information Form -->
            <x-ui.card title="Personal Profile Information" subtitle="Update your name, email, and phone contact details.">
                <form wire:submit.prevent="updateProfileInformation" class="space-y-5">
                    <!-- Profile Photo File Upload -->
                    <x-form.group label="Upload Profile Photo" name="avatar" help="JPG, PNG, or WEBP up to 2MB.">
                        <x-form.file-input wire:model="avatar" accept="image/*" />
                        <div wire:loading wire:target="avatar" class="text-xs text-indigo-600 font-semibold mt-1">
                            Uploading photo...
                        </div>
                    </x-form.group>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.group label="Full Name" name="name" required>
                            <x-form.input wire:model="name" placeholder="John Doe" />
                        </x-form.group>

                        <x-form.group label="Email Address" name="email" required>
                            <x-form.input wire:model="email" type="email" placeholder="john@smartschool.edu" />
                        </x-form.group>
                    </div>

                    <x-form.group label="Phone Number" name="phone" help="Formatted phone number for SMS notifications.">
                        <x-form.input wire:model="phone" placeholder="+1 (555) 019-2834" />
                    </x-form.group>

                    <div class="flex justify-end">
                        <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                            Save Profile Changes
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <!-- Password Security Form -->
            <x-ui.card title="Update Password Security" subtitle="Ensure your account is using a strong, unique password.">
                <form wire:submit.prevent="updatePassword" class="space-y-5">
                    <x-form.group label="Current Password" name="current_password" required>
                        <x-form.input wire:model="current_password" type="password" placeholder="••••••••" />
                    </x-form.group>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.group label="New Password" name="new_password" required>
                            <x-form.input wire:model="new_password" type="password" placeholder="••••••••" />
                        </x-form.group>

                        <x-form.group label="Confirm New Password" name="new_password_confirmation" required>
                            <x-form.input wire:model="new_password_confirmation" type="password" placeholder="••••••••" />
                        </x-form.group>
                    </div>

                    <div class="flex justify-end">
                        <x-ui.button type="submit" variant="danger" wire:loading.attr="disabled">
                            Update Security Password
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</div>
