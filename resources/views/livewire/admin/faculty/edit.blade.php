<?php

use App\Models\FacultyProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] #[Title('Edit Faculty')] class extends Component
{
    public FacultyProfile $faculty;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public function mount(FacultyProfile $faculty): void
    {
        $this->faculty = $faculty;
        $this->first_name = $faculty->first_name;
        $this->last_name = $faculty->last_name;
        $this->email = $faculty->email;
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $this->faculty->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        // Redirect back to the list
        $this->redirectRoute('admin.faculty', navigate: true);
    }
};
?>

<div class="max-w-4xl mx-auto py-6">
  <div class="flex items-center justify-between mb-6">
    <flux:heading size="xl">Edit Faculty Profile</flux:heading>
  </div>

  <form wire:submit="save"
    class="space-y-6 bg-white dark:bg-zinc-900 p-6 rounded-lg border border-zinc-200 dark:border-zinc-800">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <flux:input label="First Name" wire:model="first_name" />
      <flux:input label="Last Name" wire:model="last_name" />
      <flux:input label="Email" wire:model="email" type="email" class="md:col-span-2" />
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <flux:button href="{{ route('admin.faculty') }}" wire:navigate variant="ghost">Cancel</flux:button>
      <flux:button type="submit" variant="primary">Save Changes</flux:button>
    </div>
  </form>
</div>