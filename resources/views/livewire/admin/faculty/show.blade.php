<?php

use Livewire\Volt\Component;
use App\Models\FacultyProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('View Faculty')] class extends Component {
    public FacultyProfile $faculty;

    public function mount(FacultyProfile $faculty): void
    {
        $this->faculty = $faculty;
    }
};
?>

<div class="max-w-4xl mx-auto py-6">
  <div class="flex items-center justify-between mb-6">
    <flux:heading size="xl">{{ $faculty->first_name }} {{ $faculty->last_name }}</flux:heading>

    <div class="flex gap-2">
      <flux:button href="{{ route('admin.faculty') }}" wire:navigate icon="arrow-left">Back to List</flux:button>
      <flux:button href="{{ route('admin.faculty.edit', $faculty) }}" wire:navigate variant="primary" icon="pencil">Edit
      </flux:button>
    </div>
  </div>

  <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-4">
    <div>
      <flux:label>Full Name</flux:label>
      <div class="text-lg font-medium">{{ $faculty->first_name }} {{ $faculty->last_name }}</div>
    </div>

    <div>
      <flux:label>Email Address</flux:label>
      <div class="text-lg">{{ $faculty->email }}</div>
    </div>

    <div>
      <flux:label>Created At</flux:label>
      <div class="text-gray-500">{{ $faculty->created_at->format('F j, Y g:i A') }}</div>
    </div>
  </div>
</div>