<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>


<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-lg font-black">Deparments</h1>

        <div>
            <flux:modal.trigger name="import-faculty-modal">
                <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Data</flux:button>
            </flux:modal.trigger>

            <flux:modal.trigger name="add-faculty-modal">
                <flux:button variant="primary" size="sm" icon="plus">New Department</flux:button>
            </flux:modal.trigger>
        </div>
    </div>


    <livewire:admin.department-table lazy />
</div>