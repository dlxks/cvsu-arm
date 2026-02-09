<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Logic to get current user is handled by Blade's Auth helper
}; ?>

<div class="p-8">
    <flux:heading size="xl" level="1">Welcome, {{ auth()->user()->name }}</flux:heading>
    <flux:subheading>Role: {{ ucfirst(auth()->user()->role) }}</flux:subheading>

    <flux:separator class="my-6" />

    <div class="flex gap-4">
        @if(auth()->user()->role === 'faculty')
        <flux:card>
            <h3 class="font-semibold">Faculty Dashboard</h3>
            <p class="text-zinc-500">Access your schedules and grades here.</p>
        </flux:card>
        @endif

        @if(auth()->user()->role === 'admin')
        <flux:card>
            <h3 class="font-semibold">Admin Dashboard</h3>
            <p class="text-zinc-500">Manage users and system settings.</p>
        </flux:card>
        @endif
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <flux:button type="submit" variant="danger">Logout</flux:button>
    </form>
</div>