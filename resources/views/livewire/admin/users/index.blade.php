<div>
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">User Directory</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">User Management</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage user accounts, RBAC roles, and status activation.</p>
        </div>

        @can('create', App\Models\User::class)
            <x-ui.button variant="primary" size="md" wire:click="openCreateModal">
                <x-slot:icon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </x-slot:icon>
                Add New User
            </x-ui.button>
        @endcan
    </div>

    <!-- Filters Bar -->
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search Input -->
            <x-form.group label="Search Users">
                <x-form.input wire:model.live.debounce.300ms="search" placeholder="Search by name, email, phone..." />
            </x-form.group>

            <!-- Status Filter -->
            <x-form.group label="Account Status">
                <x-form.select wire:model.live="statusFilter" :options="['' => 'All Statuses', 'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']" />
            </x-form.group>

            <!-- Role Filter -->
            <x-form.group label="Filter by Role">
                <select wire:model.live="roleFilter" class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="">All Roles</option>
                    @foreach ($allRoles as $r)
                        <option value="{{ $r->slug }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </x-form.group>
        </div>
    </x-ui.card>

    <!-- Users Table -->
    <x-ui.card>
        <x-table.table>
            <x-table.header>
                <x-table.cell header>User Details</x-table.cell>
                <x-table.cell header>Assigned Roles</x-table.cell>
                <x-table.cell header>Status</x-table.cell>
                <x-table.cell header>Last Login</x-table.cell>
                <x-table.cell header class="text-right">Actions</x-table.cell>
            </x-table.header>
            <x-table.body>
                @forelse ($users as $user)
                    <x-table.row>
                        <!-- Name & Contact -->
                        <x-table.cell>
                            <div class="flex items-center space-x-3">
                                @if ($user->avatar_path)
                                    <img src="{{ asset('storage/uploads/' . $user->avatar_path) }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-500/30" />
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }} • {{ $user->phone ?? 'No phone' }}</p>
                                </div>
                            </div>
                        </x-table.cell>

                        <!-- Roles Badges -->
                        <x-table.cell>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <x-ui.badge variant="primary" size="sm">{{ $role->name }}</x-ui.badge>
                                @empty
                                    <span class="text-xs text-slate-400 italic">No role</span>
                                @endforelse
                            </div>
                        </x-table.cell>

                        <!-- Status Badge -->
                        <x-table.cell>
                            @if ($user->status === 'active')
                                <x-ui.badge variant="success" size="sm">Active</x-ui.badge>
                            @elseif ($user->status === 'suspended')
                                <x-ui.badge variant="danger" size="sm">Suspended</x-ui.badge>
                            @else
                                <x-ui.badge variant="warning" size="sm">Inactive</x-ui.badge>
                            @endif
                        </x-table.cell>

                        <!-- Last Login Timestamp -->
                        <x-table.cell>
                            <span class="text-xs font-mono text-slate-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </span>
                        </x-table.cell>

                        <!-- Actions -->
                        <x-table.cell class="text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @can('update', $user)
                                    <x-ui.button variant="ghost" size="sm" wire:click="openEditModal({{ $user->id }})">
                                        Edit
                                    </x-ui.button>
                                    <x-ui.button variant="outline" size="sm" wire:click="toggleUserStatus({{ $user->id }})">
                                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </x-ui.button>
                                @endcan
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="5">
                            <x-ui.empty-state title="No Users Found" description="No user accounts matched your search criteria." />
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table.table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-ui.card>

    <!-- Create/Edit User Drawer Modal -->
    <x-ui.drawer name="user-drawer" title="{{ $editingUserId ? 'Edit User Account' : 'Create New User Account' }}">
        @if ($showUserModal)
            <form wire:submit.prevent="saveUser" class="space-y-4">
                <x-form.group label="Full Name" name="name" required>
                    <x-form.input wire:model="name" placeholder="John Doe" />
                </x-form.group>

                <x-form.group label="Email Address" name="email" required>
                    <x-form.input wire:model="email" type="email" placeholder="john@smartschool.edu" />
                </x-form.group>

                <x-form.group label="Phone Number" name="phone">
                    <x-form.input wire:model="phone" placeholder="+1 (555) 019-2834" />
                </x-form.group>

                <x-form.group label="Account Status" name="status" required>
                    <x-form.select wire:model="status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']" />
                </x-form.group>

                <x-form.group label="{{ $editingUserId ? 'New Password (Leave blank to keep existing)' : 'Account Password' }}" name="password" :required="!$editingUserId">
                    <x-form.input wire:model="password" type="password" placeholder="••••••••" />
                </x-form.group>

                <!-- Roles Checklist -->
                <div class="space-y-2 pt-2">
                    <x-form.label required>Assign RBAC Roles</x-form.label>
                    <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                        @foreach ($allRoles as $role)
                            <label class="flex items-center space-x-2 text-xs cursor-pointer">
                                <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" class="rounded text-indigo-600 focus:ring-indigo-500" />
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $role->name }}</span>
                                <span class="text-slate-400">({{ $role->description }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <x-ui.button variant="ghost" type="button" wire:click="$set('showUserModal', false)">
                        Cancel
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $editingUserId ? 'Save User Changes' : 'Create User Account' }}
                    </x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.drawer>
</div>
