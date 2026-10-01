<div>
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">RBAC Roles</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Role Management</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Define role hierarchies and map module permissions.</p>
        </div>

        @can('create', App\Models\Role::class)
            <x-ui.button variant="primary" size="md" wire:click="openCreateModal">
                <x-slot:icon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </x-slot:icon>
                Create New Role
            </x-ui.button>
        @endcan
    </div>

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach ($roles as $role)
            <x-ui.card>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $role->name }}</h3>
                            <x-ui.badge variant="neutral" size="sm">{{ $role->slug }}</x-ui.badge>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $role->description ?? 'No description provided.' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 my-4"></div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">
                        <strong class="text-slate-800 dark:text-slate-200">{{ $role->users_count }}</strong> Users Assigned
                    </span>
                    <span class="text-slate-500">
                        <strong class="text-indigo-600 dark:text-indigo-400">{{ $role->permissions_count }}</strong> Permissions
                    </span>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end">
                        @can('update', $role)
                            <x-ui.button variant="ghost" size="sm" wire:click="openEditModal({{ $role->id }})">
                                Configure Permissions
                            </x-ui.button>
                        @endcan
                    </div>
                </x-slot:footer>
            </x-ui.card>
        @endforeach
    </div>

    <!-- Create/Edit Role Drawer Modal -->
    <x-ui.drawer name="role-drawer" title="{{ $editingRoleId ? 'Configure Role Permissions' : 'Create New Role' }}">
        @if ($showRoleModal)
            <form wire:submit.prevent="saveRole" class="space-y-4">
                <x-form.group label="Role Title" name="name" required>
                    <x-form.input wire:model="name" placeholder="e.g. Academic Coordinator" />
                </x-form.group>

                <x-form.group label="Description" name="description">
                    <x-form.textarea wire:model="description" placeholder="Specify responsibilities of this role..." rows="2" />
                </x-form.group>

                <!-- Permissions Matrix grouped by Module -->
                <div class="space-y-3 pt-2">
                    <x-form.label required>Module Permission Matrix</x-form.label>

                    <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                        @foreach ($permissionsGrouped as $group => $perms)
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                <h4 class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                    {{ ucfirst($group) }} Module
                                </h4>
                                <div class="space-y-2">
                                    @foreach ($perms as $p)
                                        <label class="flex items-center space-x-2 text-xs cursor-pointer">
                                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $p->id }}" class="rounded text-indigo-600 focus:ring-indigo-500" />
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $p->name }}</span>
                                            <span class="text-slate-400 text-[10px]">({{ $p->slug }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <x-ui.button variant="ghost" type="button" wire:click="$set('showRoleModal', false)">
                        Cancel
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $editingRoleId ? 'Save Permissions' : 'Create Role' }}
                    </x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.drawer>
</div>
