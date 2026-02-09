<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Admin specific logic can go here
}; ?>

<div class="p-8">
    <flux:heading size="xl" level="1">Admin Dashboard</flux:heading>
    <flux:subheading>Welcome, {{ auth()->user()->name }}</flux:subheading>

    <flux:separator class="my-6" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:card>
            <flux:icon.users class="mb-2 text-zinc-500" />
            <h3 class="font-semibold text-lg">User Management</h3>
            <p class="text-zinc-500 text-sm mb-4">Add, remove, or modify system users.</p>

            {{-- <flux:button href="{{ route('admin.users') }}" size="sm" variant="primary" class="w-full">
                Manage Users
            </flux:button> --}}
        </flux:card>

        <flux:card>
            <flux:icon.cog class="mb-2 text-zinc-500" />
            <h3 class="font-semibold text-lg">System Settings</h3>
            <p class="text-zinc-500 text-sm mb-4">Configure application defaults.</p>

            <flux:button href="#" size="sm" class="w-full">
                Settings
            </flux:button>
        </flux:card>

        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <flux:button type="submit" variant="danger">Logout</flux:button>
        </form>
    </div>
</div>