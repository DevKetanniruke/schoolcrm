<div>
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">System Permissions</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Permission Management</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">View fine-grained permission nodes grouped by CRM feature modules.</p>
        </div>

        @can('create', App\Models\Permission::class)
            <x-ui.button variant="primary" size="md" wire:click="openCreateModal">
                <x-slot:icon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </x-slot:icon>
                Create Permission
            </x-ui.button>
        @endcan
    </div>

    <!-- Permissions List Grouped by Module -->
    <div class="space-y-6">
        @foreach ($permissionsGrouped as $group => $perms)
            <x-ui.card title="{{ ucfirst($group) }} Module Permissions" subtitle="{{ count($perms) }} registered permissions">
                <x-table.table>
                    <x-table.header>
                        <x-table.cell header>Permission Label</x-table.cell>
                        <x-table.cell header>Permission Slug</x-table.cell>
                        <x-table.cell header>Description</x-table.cell>
                        <x-table.cell header class="text-right">Action</x-table.cell>
                    </x-table.header>
                    <x-table.body>
                        @foreach ($perms as $perm)
                            <x-table.row>
                                <x-table.cell>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $perm->name }}</span>
                                </x-table.cell>
                                <x-table.cell>
                                    <code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400 font-mono">{{ $perm->slug }}</code>
                                </x-table.cell>
                                <x-table.cell>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $perm->description ?? 'No description' }}</span>
                                </x-table.cell>
                                <x-table.cell class="text-right">
                                    @can('update', $perm)
                                        <x-ui.button variant="ghost" size="sm" wire:click="openEditModal({{ $perm->id }})">
                                            Edit
                                        </x-ui.button>
                                    @endcan
                                </x-table.cell>
                            </x-table.row>
                        @endforeach
                    </x-table.body>
                </x-table.table>
            </x-ui.card>
        @endforeach
    </div>

    <!-- Create/Edit Permission Drawer Modal -->
    <x-ui.drawer name="permission-drawer" title="{{ $editingPermissionId ? 'Edit Permission Node' : 'Create New Permission Node' }}">
        @if ($showPermissionModal)
            <form wire:submit.prevent="savePermission" class="space-y-4">
                <x-form.group label="Module Group" name="group" required>
                    <x-form.input wire:model="group" placeholder="e.g. users, students, finance" />
                </x-form.group>

                <x-form.group label="Permission Name" name="name" required>
                    <x-form.input wire:model="name" placeholder="e.g. Export Fee Reports" />
                </x-form.group>

                <x-form.group label="Description" name="description">
                    <x-form.textarea wire:model="description" placeholder="Specify scope of this permission..." rows="2" />
                </x-form.group>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <x-ui.button variant="ghost" type="button" wire:click="$set('showPermissionModal', false)">
                        Cancel
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $editingPermissionId ? 'Save Changes' : 'Create Permission' }}
                    </x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.drawer>
</div>
