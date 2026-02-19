<?php

use App\Models\FacultyProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;

new #[Layout('layouts.app')] #[Title('View Faculty')] class extends Component
{
    use Interactions;

    public FacultyProfile $faculty;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $branch = '';
    public string $department = '';

    public bool $editMode = false;

    public function mount(FacultyProfile $faculty): void
    {
        $this->faculty = $faculty;
        $this->fillProps();
    }

    public function fillProps(): void
    {
        $this->first_name = $this->faculty->first_name;
        $this->last_name = $this->faculty->last_name;
        $this->email = $this->faculty->email;
        $this->branch = $this->faculty->branch ?? '';
        $this->department = $this->faculty->department ?? '';
    }

    public function enableEdit(): void
    {
        $this->editMode = true;
    }

    public function cancelEdit(): void
    {
        $this->editMode = false;
        $this->fillProps(); // Revert changes to original values
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'branch'     => 'required|string|max:255',
            'department' => 'required|string|max:255',
        ]);

        $this->faculty->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'branch'     => $this->branch,
            'department' => $this->department,
        ]);

        $this->editMode = false;
        
        $this->toast()
            ->success('Success', 'Faculty profile updated successfully.')
            ->send();
    }
};
?>

<div class="max-w-4xl mx-auto py-6">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">
      {{ $faculty->first_name }} {{ $faculty->last_name }}
    </h1>

    <div class="flex gap-2">
      <x-button href="{{ route('admin.faculty') }}" wire:navigate icon="arrow-left" color="green" outline>
        Back to List
      </x-button>

      @if(!$editMode)
      <x-button icon="pencil" color="primary" x-on:click="$modalOpen('edit-confirmation-modal')">
        Edit Information
      </x-button>
      @endif
    </div>
  </div>

  {{-- Main Form Container --}}
  <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg border border-zinc-200 dark:border-zinc-800 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <x-input label="First Name" wire:model="first_name" :disabled="!$editMode" xs />
      <x-input label="Last Name" wire:model="last_name" :disabled="!$editMode" sm />

      <x-input label="Email" wire:model="email" type="email" class="md:col-span-2" :disabled="!$editMode" />

      {{-- Using Native Select for Branch to ensure options match, or Input if free-text is preferred --}}
      <div class="col-span-1">
        @if($editMode)
        <x-select.native label="Branch" wire:model="branch" :options="['Main', 'Extension']" />
        @else
        <x-input label="Branch" wire:model="branch" disabled />
        @endif
      </div>

      <x-input label="Department" wire:model="department" :disabled="!$editMode" />
    </div>

    @if($editMode)
    <div class="flex justify-end gap-2 pt-6 border-t border-zinc-100 dark:border-zinc-800 mt-6">
      <x-button wire:click="cancelEdit" color="slate" flat>Cancel</x-button>
      <x-button wire:click="save" color="primary" loading="save">Save Changes</x-button>
    </div>
    @endif
  </div>

  {{-- Confirmation Modal --}}
  <x-modal id="edit-confirmation-modal" title="Edit Information" blur center>
    <div class="mb-6">
      <p class="text-gray-600 dark:text-gray-300">
        Are you sure you want to enable editing for this faculty profile?
      </p>
    </div>

    <x-slot:footer>
      <div class="flex justify-end gap-2">
        <x-button color="slate" flat x-on:click="$modalClose('edit-confirmation-modal')">
          Cancel
        </x-button>
        <x-button color="primary" wire:click="enableEdit" x-on:click="$modalClose('edit-confirmation-modal')">
          Confirm
        </x-button>
      </div>
    </x-slot:footer>
  </x-modal>
</div>