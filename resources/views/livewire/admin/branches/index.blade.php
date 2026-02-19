<?php
use App\Imports\BranchesImport;
use App\Models\Branch;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

new class extends Component
{
    use Interactions, WithFileUploads;

    // Form Properties
    public ?int $editingId = null; // Numeric primary key
    public $branch_id = '';       // Custom ID (e.g., CvSU-M001)
    public $code = '';
    public $name = '';
    public $address = '';
    public $type = 'Main';
    public $is_active = true;
    public $isEditing = false;

    // Import Property
    public $file;

    public function resetForm()
    {
        $this->reset(['editingId', 'branch_id', 'code', 'name', 'address', 'type', 'file', 'is_active', 'isEditing']);
        $this->resetErrorBag();
        $this->type = 'Main';
        if (!$this->isEditing) {
            $this->generateId();
        }
    }

    public function create()
    {
        $this->isEditing = false;
        $this->resetForm();
        Flux::modal('branch-modal')->show();
    }

    #[On('edit-branch')]
    public function edit($id)
    {
        $this->resetForm();
        $branchModel = Branch::findOrFail($id);

        $this->editingId = $branchModel->id;
        $this->branch_id = $branchModel->branch_id;
        $this->code = $branchModel->code;
        $this->name = $branchModel->name;
        $this->address = $branchModel->address ?? '';
        $this->type = $branchModel->type;
        $this->is_active = (bool) $branchModel->is_active;
        $this->isEditing = true;

        Flux::modal('branch-modal')->show();
    }

    public function updatedType($value)
    {
        if (!$this->isEditing) {
            $this->generateId();
        }
    }

    public function generateId()
    {
        // Calls the static method on your Branch model
        $this->branch_id = Branch::generateNextId($this->type);
    }

    public function save()
    {
        $validationRules = [
            'type' => 'required|string|in:Main,Satellite',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'branch_id' => [
                'required', 'string',
                Rule::unique('branches', 'branch_id')->ignore($this->editingId),
            ],
            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('branches', 'code')->ignore($this->editingId),
            ],
        ];

        $validated = $this->validate($validationRules);

        $data = [
            'branch_id' => $this->branch_id,
            'code'      => $this->code,
            'name'      => $this->name,
            'address'   => $this->address,
            'type'      => $this->type,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Branch::findOrFail($this->editingId)->update($data);
            $message = 'Branch updated successfully.';
        } else {
            Branch::create($data);
            $message = 'Branch created successfully.';
        }

        Flux::modal('branch-modal')->close();
        $this->dispatch('pg:eventRefresh-branches-table');
        $this->resetForm();
        $this->toast()->success('Success', $message)->send();
    }

    public function import()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        try {
            Excel::import(new BranchesImport, $this->file);
            Flux::modal('import-branches-modal')->close();
            $this->dispatch('pg:eventRefresh-branches-table');
            $this->reset('file');
            $this->toast()->success('Success', 'Branches imported successfully.')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Import failed: '.$e->getMessage())->send();
        }
    }
}; ?>

<div class="space-y-4">
  {{-- Header --}}
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <h1 class="text-lg font-black">Branches/Colleges Management</h1>

    <div class="flex gap-2">
      <flux:modal.trigger name="import-branches-modal">
        <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Data</flux:button>
      </flux:modal.trigger>

      <flux:button variant="primary" size="sm" icon="plus" wire:click="create">New Branch</flux:button>
    </div>
  </div>

  {{-- Table Section --}}
  <div class="relative min-h-100">
    <div wire:loading wire:target="import, save, edit"
      class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800">
      <flux:icon.arrow-path class="animate-spin w-10 h-10 text-primary mb-2" />
      <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Processing...</p>
    </div>
    <livewire:admin.branches-table />
  </div>

  {{-- Unified Branch Modal --}}
  <flux:modal name="branch-modal" class="md:w-120">
    <form wire:submit="save" class="space-y-6">
      <div>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Branch' : 'Add New Branch' }}</flux:heading>
        <flux:subheading>{{ $isEditing ? 'Update details.' : 'Enter details for creation.' }}</flux:subheading>
      </div>

      <div class="space-y-4">
        <flux:select wire:model.live="type" label="Type" size="sm">
          <flux:select.option value="Main">Main</flux:select.option>
          <flux:select.option value="Satellite">Satellite</flux:select.option>
        </flux:select>

        <div class="grid grid-cols-2 gap-4">
          <div class="relative">
            <flux:input wire:model="branch_id" label="Branch ID" size="sm" />
            <button type="button" wire:click="generateId"
              class="absolute top-8 right-2 text-zinc-400 hover:text-zinc-600">
              <flux:icon.arrow-path class="w-4 h-4" />
            </button>
          </div>
          <flux:input wire:model="code" label="Short Code" size="sm" />
        </div>

        <flux:input wire:model="name" label="Branch Name" size="sm" />
        <flux:input wire:model="address" label="Address" size="sm" />
        <flux:checkbox wire:model="is_active" label="Operational (Active)" />
      </div>

      <div class="flex mt-6">
        <flux:spacer />
        <flux:button variant="ghost" x-on:click="$flux.modal('branch-modal').close()">Cancel</flux:button>
        <flux:button type="submit" variant="primary">
          {{ $isEditing ? 'Save Changes' : 'Create Branch' }}
        </flux:button>
      </div>
    </form>
  </flux:modal>

  {{-- Import Modal --}}
  <flux:modal name="import-branches-modal" class="md:w-96">
    <form wire:submit="import" class="space-y-4">
      <div>
        <flux:heading size="lg">Import Branches</flux:heading>
        <flux:subheading>Upload Excel/CSV file.</flux:subheading>
      </div>
      <flux:input type="file" wire:model="file" size="sm" accept=".csv, .xlsx, .xls" />
      <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Start Import</flux:button>
      </div>
    </form>
  </flux:modal>
</div>