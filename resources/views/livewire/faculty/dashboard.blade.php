<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Faculty specific logic
}; ?>

<div class="p-8">
    <flux:heading size="xl" level="1">Faculty Dashboard</flux:heading>
    <flux:subheading>Academic Term: 2025-2026</flux:subheading>

    <flux:separator class="my-6" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <flux:card>
            <h3 class="font-semibold">My Subjects</h3>
            <p class="text-zinc-500 text-sm mb-4">View and manage your assigned subjects.</p>
            {{-- <flux:button href="{{ route('faculty.subjects') }}" size="sm" class="w-full">
                View Subjects
            </flux:button> --}}
        </flux:card>

        <flux:card>
            <h3 class="font-semibold">Class Schedule</h3>
            <p class="text-zinc-500 text-sm mb-4">Check your upcoming classes.</p>
            <flux:button href="#" size="sm" class="w-full">
                View Schedule
            </flux:button>
        </flux:card>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <flux:button type="submit" variant="danger">Logout</flux:button>
    </form>
</div>