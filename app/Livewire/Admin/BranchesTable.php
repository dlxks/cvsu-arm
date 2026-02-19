<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use TallStackUi\Traits\Interactions;

/**
 * Branches and Colleges Management Table
 * * Manages institutional locations (Main campus and satellite campuses)
 */
final class BranchesTable extends PowerGridComponent
{
    /**
     * Property setup of BranchesTable Powergrid component.
     */
    use Interactions, WithExport;

    public string $tableName = 'branches-table';

    // State for sorting
    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    // State for deletion
    public ?string $deleteId = null;

    // State for force deletion
    public ?string $forceDeleteId = null;

    /**
     * Override the bood method of PowerGridComponent
     */
    public function boot(): void
    {
        // Place filters outside the table header
        config(['livewire-powergrid.filter' => 'outside']);
    }

    /**
     * Set up functions and features that shows on header and footer.
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            // Set up export options
            PowerGrid::exportable(fileName: 'branch-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            // Set up header with search, column toggle, enable soft delete filter, and custom header view
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true)
                ->includeViewOnTop('livewire.admin.branches.header-actions'),

            // Set up footer to display pagination and current record count
            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),

            // Enable responsive design for the table
            PowerGrid::responsive()
                ->fixedColumns('code', Responsive::ACTIONS_COLUMN_NAME),
        ];
    }

    /**
     * PowerGrid datasource.
     *
     * @return Builder<Branch>
     */
    public function datasource(): Builder
    {
        return Branch::query()
            ->when($this->softDeletes === 'withTrashed', fn ($query) => $query->withTrashed())
            ->when($this->softDeletes === 'onlyTrashed', fn ($query) => $query->onlyTrashed());
    }

    /**
     * Define custom header buttons
     */
    public function header(): array
    {
        // Check if there are rows selected
        $hasSelection = "window.pgBulkActions.count('{$this->tableName}') > 0";

        return [
            // Custom bulk delete button
            Button::add('bulk-delete')
                ->icon('default-trash', ['class' => 'w-5 h-5 text-red-500 transition duration-150'])
                ->slot('Delete(<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)')
                ->class('px-2 py-2 mr-1 inline-flex items-center justify-center border border-red-500 text-red-500 rounded-md cursor-pointer')
                ->attributes([
                    'x-show' => $hasSelection,
                    'x-cloak' => true,
                    'title' => 'Delete Selected Data',
                ])
                ->dispatch('open-bulk-delete-modal', []),

            // Custom bulk restore button
            Button::add('bulk-restore')
                ->icon('default-arrow-path', ['class' => 'w-5 h-5 text-amber-500 transition duration-150'])
                ->slot('Restore (<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)')
                ->class('px-2 py-2 mr-1 inline-flex items-center justify-center border border-amber-500 text-amber-500 rounded-md cursor-pointer')
                ->attributes([
                    'x-show' => $hasSelection,
                    'x-cloak' => true,
                    'title' => 'Restore Data',
                ])
                ->dispatch('bulk-restore', []),
        ];
    }

    /**
     * PowerGrid field setup
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('branch_id') // Maps to the string ID (CvSU-M001)
            ->add('code') // Maps to the short code (CEIT)
            ->add('type') // Maps type (Main/Satellite)
            ->add('name') // Displays the branch name
            ->add('address') // Displays the branch address/location
            ->add('created_at_formatted', fn (Branch $model) => $model->created_at->format('d/m/Y')) // Display formatted date of creation of data

            // STATUS LABEL: For is_active
            ->add('is_active', fn ($branch) => $branch->is_active ? 'Active' : 'Inactive')
            ->add('is_active_badge', function (Branch $model) {
                $status = $model->is_active ? 'Active' : 'Inactive';
                $colors = $model->is_active
                    ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                    : 'bg-red-50 text-amber-700 ring-amber-600/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20';

                return "<span class='inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$colors}'>
                {$status}
            </span>";
            });
    }

    /**
     * PowerGrid column setup and properties set up
     */
    public function columns(): array
    {
        return [
            // Display the String ID (CvSU-M001)
            Column::make('Branch ID', 'branch_id')
                ->sortable()
                ->searchable()
                ->bodyAttribute('text-sm font-mono'), // Font-mono helps with ID readability

            // Display the branch code
            Column::add()
                ->title('Short Code')
                ->field('code')
                ->sortable()
                ->searchable()
                ->bodyAttribute('text-sm'),

            // Display the branch type (Main/Satellite)
            Column::add()->title('Type')
                ->field('type')
                ->sortable()
                ->searchable()
                ->bodyAttribute('text-sm'),

            // Display full branch name
            Column::add()->title('Name')
                ->field('name')
                ->sortable()
                ->searchable()
                ->bodyAttribute('text-sm'),

            // Display branch address/location
            Column::add()->title('Address')
                ->field('address')
                ->sortable()
                ->searchable()
                ->bodyAttribute('text-sm'),

            // Status of Satellite/Main (hidden in display shown in export)
            Column::make('Operation Status', 'is_active', 'is_active')
                ->visibleInExport(true)
                ->hidden()
                ->bodyAttribute('text-center', 'text-sm'),

            // Status of Satellite/Main (shown in display but hidden in export)
            Column::make('Operation Status', 'is_active_badge', 'is_active')
                ->visibleInExport(false)
                ->bodyAttribute('text-center', 'text-sm')
                ->bodyAttribute(),

            Column::action('Action'),
        ];
    }

    /**
     * PowerGrid filters list set up
     */
    public function filters(): array
    {
        return [
            Filter::select('type', 'type')
                ->dataSource([
                    ['name' => 'Main', 'id' => 'Main'],
                    ['name' => 'Satellite', 'id' => 'Satellite'],
                ])
                ->optionLabel('name')
                ->optionValue('id'),
        ];
    }

    /**
     * Display action buttons for each row
     */
    public function actions(Branch $row): array
    {
        // Actions for data that is soft-deleted (in trash)
        if ($row->trashed()) {
            return [
                // Restore data button
                Button::add('restore')
                    ->icon('default-arrow-path', ['class' => 'w-5 h-5 text-amber-500 group-hover:text-amber-700 transition duration-150'])
                    ->class('group cursor-pointer')
                    ->attributes(['title' => 'Restore Branch'])
                    ->dispatch('restore', ['id' => $row->id]),

                // Permanently delete data button
                Button::add('force-delete')
                    ->icon('default-trash', ['class' => 'w-5 h-5 text-red-600 group-hover:text-red-800 transition duration-150'])
                    ->class('group cursor-pointer')
                    ->attributes(['title' => 'Delete Permanently'])
                    ->dispatch('confirm-force-delete', ['id' => $row->id]),
            ];
        }

        // Default actions for active data (not deleted)
        return [
            // Edit button
            Button::add('edit')
                ->icon('default-pencil-square', ['class' => 'w-5 h-5 text-blue-500 group-hover:text-blue-700'])
                ->class('group cursor-pointer')
                ->dispatch('edit-branch', ['id' => $row->id]), // Sending string ID

            // Move to Trash button
            Button::add('delete')
                ->icon('default-trash', ['class' => 'w-5 h-5 text-red-500 group-hover:text-red-700 transition'])
                ->class('group cursor-pointer')
                ->attributes(['title' => 'Delete Branch'])
                ->dispatch('confirm-delete', ['id' => $row->id]),
        ];
    }

    /**
     * EVENTS & LISTENERS
     */

    // Open modal for delete confirmation
    #[On('confirm-delete')]
    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        Flux::modal('delete-confirmation')->show();
    }

    // Open modal for bulk delete confirmation
    #[On('open-bulk-delete-modal')]
    public function openBulkDeleteModal()
    {
        if (count($this->checkboxValues) > 0) {
            Flux::modal('bulk-delete-confirmation')->show();
        }
    }

    // Open modal for force delete confirmation
    #[On('confirm-force-delete')]
    public function confirmForceDelete(int $id)
    {
        $this->forceDeleteId = $id;
        Flux::modal('force-delete-confirmation')->show();
    }

    /**
     * LOGIC EXECUTION / FUNCTIONS
     */

    // Execute restore data from trash (soft-deleted)
    #[On('restore')]
    public function restore(int $id)
    {
        $branch = Branch::withTrashed()->find($id);
        if ($branch) {
            $branch->restore();
            $this->toast()->success('Restored', 'Branch restored successfully.')->send();
        }
    }

    // Execute restore selected data from trash
    #[On('bulk-restore')]
    public function bulkRestore()
    {
        // Count number of selected records
        $count = count($this->checkboxValues);
        if ($count === 0) {
            $this->toast()->warning('Warning', 'No records selected.')->send();

            return;
        }

        // Restore and set active inside the function
        $branches = Branch::onlyTrashed()->whereIn('id', $this->checkboxValues)->get();
        foreach ($branches as $branch) {
            $branch->restore();
            $branch->update(['is_active' => 1]);
        }
        $this->checkboxValues = []; // Clear selected data
        $this->toast()->success('Success', "$count records restored successfully.")->send(); // Display success message via toast
    }

    // Execute bulk delete of selected data
    #[On('bulk-delete')]
    public function bulkDestroy(): void
    {
        $count = count($this->checkboxValues);
        if ($count === 0) {
            $this->toast()->warning('Warning', 'No records selected.')->send();

            return;
        }

        $branches = Branch::withTrashed()->whereIn('id', $this->checkboxValues)->get();

        foreach ($branches as $branch) {
            // Execute force delete if data is soft-delete
            if ($branch->trashed()) {
                $branch->forceDelete();
            }
            // Execute soft delete (move to trash)
            else {
                $branch->delete();
                $branch->updated(['is_active' => 0]);
            }
        }

        $this->checkboxValues = [];
        Flux::modal('bulk-delete-confirmation')->close();
        $this->dispatch('pg:eventRefresh-default');
        $this->toast()->success('Success', "$count records deleted successfully.")->send();
    }

    // Execute soft delete (move to trash)
    #[On('delete')]
    public function destroy(): void
    {
        if ($this->deleteId) {
            $branch = Branch::find($this->deleteId);
            if ($branch) {
                $branch->delete();
                $branch->update(['is_active' => 0]);
            }

            $this->deleteId = null;
            Flux::modal('delete-confirmation')->close();
            $this->dispatch('pg:eventRefresh-default');
            $this->toast()->success('Removed', 'Branch moved to trash.')->send();
        }
    }

    // Execute permanent delete of data
    #[On('force-delete')]
    public function forceDestroy(): void
    {
        if ($this->forceDeleteId) {
            Branch::withTrashed()->find($this->forceDeleteId)?->forceDelete();

            $this->forceDeleteId = null;
            Flux::modal('force-delete-confirmation')->close();
            $this->dispatch('pg:eventRefresh-default');
            $this->toast()->success('Removed', 'Branch permanently deleted.')->send();
        }
    }
}
