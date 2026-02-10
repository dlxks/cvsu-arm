<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with(): array
    {
        return [
            'title' => 'Admin Dashboard',
        ];
    }
}; ?>

<div class="space-y-4">
    <flux:heading size="lg" level="1">Admin Dashboard</flux:heading>
    <flux:subheading>Welcome, {{ auth()->user()->name }}</flux:subheading>

    <flux:separator class="my-6" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:card>
            <flux:icon.users class="mb-2" />
            <h3 class="font-semibold text-lg">User Management</h3>
            <p class="text-sm mb-4">Add, remove, or modify system users.</p>
            <flux:button href="#" size="sm" variant="primary" class="w-full">
                Manage Users
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:icon.cog class="mb-2 " />
            <h3 class="font-semibold text-lg">System Settings</h3>
            <p class="text-sm mb-4">Configure application defaults.</p>
            <flux:button href="#" size="sm" class="w-full">
                Settings
            </flux:button>
        </flux:card>
    </div>
</div>