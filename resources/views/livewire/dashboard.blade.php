<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // ...
}; ?>

<div class="p-8">
    <flux:heading size="xl" level="1">Welcome, {{ auth()->user()->name }}</flux:heading>

    <flux:subheading>
        Roles: {{ auth()->user()->getRoleNames()->implode(', ') }}
    </flux:subheading>

    <flux:separator class="my-6" />

    <div class="flex gap-4">
        @role('faculty')
        <flux:card>
            <h3 class="font-semibold">Faculty Dashboard</h3>
            <p class="text-zinc-500">Access your schedules and grades here.</p>
        </flux:card>
        @endrole

        @role('admin')
        <flux:card>
            <h3 class="font-semibold">Admin Dashboard</h3>
            <p class="text-zinc-500">Manage users and system settings.</p>

            <div class="mt-4">
                <flux:button href="/admin/users" size="sm">Manage Users</flux:button>
            </div>
        </flux:card>
        @endrole
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <flux:button type="submit" variant="danger">Logout</flux:button>
    </form>
</div>