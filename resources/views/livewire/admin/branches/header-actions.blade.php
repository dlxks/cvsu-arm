<div>
  {{-- Success Message --}}
  @if (session()->has('message'))
  <div
    class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded shadow-lg transition transform duration-500"
    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
    {{ session('message') }}
  </div>
  @endif

  {{-- Create/Edit Modal --}}
  <div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50" style="display: none;">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.away="open = false">
      <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
          {{ $isEdit ? 'Edit Branch/College' : 'Create New Branch/College' }}
        </h3>
        <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">&times;</button>
      </div>

      <div class="p-6 space-y-4">
        {{-- Type Selection --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
          <select wire:model="type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            <option value="MAIN">Main Campus (College)</option>
            <option value="EXTENSION">Extension Campus</option>
          </select>
        </div>

        {{-- Code --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code (e.g., CEIT, SILANG)</label>
          <input type="text" wire:model="code"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
          @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Name --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input type="text" wire:model="name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
          @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Address --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
          <input type="text" wire:model="address"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
          @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-end space-x-2">
        <button @click="open = false"
          class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
        <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
      </div>
    </div>
  </div>

  {{-- Import Modal --}}
  <div x-data="{ open: @entangle('showImportModal') }" x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50" style="display: none;">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4" @click.away="open = false">
      <div class="px-6 py-4 border-b dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Import Branches</h3>
      </div>
      <div class="p-6">
        <p class="text-sm text-gray-500 mb-4">Upload an Excel/CSV file with headers: <strong>type, code, name,
            address</strong>.</p>
        <input type="file" wire:model="importFile"
          class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
        @error('importFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
      </div>
      <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-end space-x-2">
        <button @click="open = false"
          class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
        <button wire:click="import" wire:loading.attr="disabled"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          <span wire:loading.remove>Import</span>
          <span wire:loading>Uploading...</span>
        </button>
      </div>
    </div>
  </div>
</div>