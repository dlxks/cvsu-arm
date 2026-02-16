<div>
  {{-- Single Delete Confirmation Modal --}}
  <flux:modal name="delete-confirmation" class="min-w-88">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete Faculty?</flux:heading>
        <flux:subheading>This action cannot be undone.</flux:subheading>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:button variant="ghost" x-on:click="$flux.modal('delete-confirmation').close()">Cancel</flux:button>
        <flux:button variant="danger" wire:click="destroy">Delete</flux:button>
      </div>
    </div>
  </flux:modal>

  {{-- Bulk Delete Confirmation Modal --}}
  <flux:modal name="bulk-delete-confirmation" class="min-w-88">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete Selected Faculty?</flux:heading>
        <flux:subheading>Are you sure you want to delete these records?</flux:subheading>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:button variant="ghost" x-on:click="$flux.modal('bulk-delete-confirmation').close()">Cancel</flux:button>
        <flux:button variant="danger" wire:click="bulkDestroy">Delete All</flux:button>
      </div>
    </div>
  </flux:modal>

  {{-- Force Delete Confirmation Modal --}}
  <flux:modal name="force-delete-confirmation" class="min-w-88">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Force Delete Faculty?</flux:heading>
        <flux:subheading>This will permanently remove the record and cannot be undone.</flux:subheading>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:button variant="ghost" x-on:click="$flux.modal('force-delete-confirmation').close()">Cancel</flux:button>
        <flux:button variant="danger" wire:click="forceDestroy">Force Delete</flux:button>
      </div>
    </div>
  </flux:modal>
</div>