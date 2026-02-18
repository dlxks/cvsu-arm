<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $academicTerm = '2025–2026';

    public function with(): array
    {
        return [
            'title' => 'Faculty Dashboard',
        ];
    }
}; ?>

<div class="space-y-6">
    <header>
        <flux:heading size="xl" level="1">{{ __('Faculty Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('Academic Term: :term', ['term' => $academicTerm]) }}</flux:subheading>
    </header>

    <flux:separator />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">{{-- My Subjects --}}
        <flux:card>
            <flux:icon.book-open class="mb-2" />

            <h3 class="font-semibold text-lg">
                {{ __('My Subjects') }}
            </h3>

            <p class="text-sm mb-4">
                {{ __('View and manage your assigned subjects and student lists.') }}
            </p>

            <flux:button href="#" size="sm" variant="primary" class="w-full">
                {{ __('View Subjects') }}
            </flux:button>
        </flux:card>

        {{-- Schedule Management --}}
        <flux:card>
            <flux:icon.calendar class="mb-2" />

            <h3 class="font-semibold text-lg">
                {{ __('Schedule Management') }}
            </h3>

            <p class="text-sm mb-4">
                {{ __('Check and manage your teaching schedule and room assignments.') }}
            </p>

            <flux:button href="#" size="sm" class="w-full">
                {{ __('View Schedule') }}
            </flux:button>
        </flux:card>

        {{-- Additional Academic Card Placeholder --}}
        <flux:card>
            {{--
            <flux:icon. class="mb-2" /> --}}

            <h3 class="font-semibold text-lg">
                {{ __('Grades & Evaluation') }}
            </h3>

            <p class="text-sm mb-4">
                {{ __('Input student grades and view performance evaluations.') }}
            </p>

            <flux:button href="#" size="sm" class="w-full" disabled>
                {{ __('Coming Soon') }}
            </flux:button>
        </flux:card>
    </div>
</div>