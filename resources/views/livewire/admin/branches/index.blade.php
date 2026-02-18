<?php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

new class extends Component
{
    use Interactions, WithFileUploads;

    public $showModal = false;

    public $showImportModal = false;

    public $isEdit = false;

    // Form fields
    public $branchId;

    public $type = 'EXTENSION';

    public $code;

    public $name;

    public $address;

    // Import field
    public $importFile;

    // Action listeners
    protected $listeners = [
        'createBracnh' => 'create',
        'editBranch' => 'edit',
        'deleteBranch' => 'delete',
        'openImportModal' => 'openImport',
    ];

    protected $rules = [
        'type' => 'required|string|in:EXTENSION,MAIN',
        'code' => 'required|string|max:50|unique:branches,code',
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
    ];
}

?>

<div class="space-y-4">
  {{-- Header --}}
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <h1 class="text-lg font-black">Branches/Colleges Management</h1>

    <div>
      <flux:modal.trigger name="import-faculty-modal">
        <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Data</flux:button>
      </flux:modal.trigger>

      <flux:modal.trigger name="add-faculty-modal">
        <flux:button variant="primary" size="sm" icon="plus">Add Branch/College</flux:button>
      </flux:modal.trigger>
    </div>
  </div>

  {{-- Table Section with Loading --}}
  <div class="relative min-h-100">
    <div wire:loading wire:target="import, save"
      class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800">
      <flux:icon.arrow-path class="animate-spin w-10 h-10 text-primary mb-2" />
      <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Updating Faculty Table...</p>
    </div>

    <livewire:admin.branches-table lazy />
  </div>
</div>