<?php

namespace App\Livewire\Admin;

use App\Models\FacultyProfile;
use App\Models\User; // Import User model
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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
use TallStackUi\Traits\Interactions; // Use Flux Facade

/**
 * Faculty Management Table
 * * Manages faculty profiles
 */
final class FacultyProfileTable extends PowerGridComponent
{
    /**
     * Property setup of BranchesTable Powergrid component.
     */
    use Interactions, WithExport;

    public string $tableName = 'facultyProfileTable';

    // State for sorting
    public string $sortField = 'first_name';

    public string $sortDirection = 'asc';

    // State for deletion
    public ?int $deleteId = null;

    // State for force deletion
    public ?int $forceDeleteId = null;

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
            PowerGrid::exportable(fileName: 'faculty-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            // Set up header with search, column toggle, enable soft delete filter, and custom header view
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true)
                ->includeViewOnTop('livewire.admin.faculty.header-actions'),

            // Set up footer to display pagination and current record count
            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),

            // Enable responsive design for the table
            PowerGrid::responsive()
                ->fixedColumns('first_name', 'last_name', Responsive::ACTIONS_COLUMN_NAME),
        ];
    }

    /**
     * PowerGrid datasource.
     *
     * @return Builder<FacultyProfile>
     */
    public function datasource(): Builder
    {
        return FacultyProfile::query()
            ->with('user')
            ->when($this->softDeletes === 'withTrashed', fn ($query) => $query->withTrashed())
            ->when($this->softDeletes === 'onlyTrashed', fn ($query) => $query->onlyTrashed());
    }

    /**
     * Define relationships for searching.
     */
    public function relationSearch(): array
    {
        return [
            'user' => ['name', 'email'],
        ];
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
                ->slot('(<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)')
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
                ->slot('(<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)')
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
            ->add('user_id')
            ->add('first_name')
            ->add('middle_name')
            ->add('last_name')
            ->add('branch')
            ->add('department')
            ->add('academic_rank')
            ->add('email')
            ->add('contactno')
            ->add('address')
            ->add('sex')
            ->add('birthday_formatted', fn (FacultyProfile $model) => $model->birthday ? Carbon::parse($model->birthday)->format('d/m/Y') : '-')
            // RAW FIELD: This is what PowerGrid uses for CSV/Excel exports
            ->add('status', fn (FacultyProfile $model) => $model->trashed() ? 'Deleted' : 'Active')
            // DISPLAY FIELD: This is what we will show in the web browser
            ->add('status_badge', function (FacultyProfile $model) {
                if ($model->trashed()) {
                    return '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">Deleted</span>';
                }

                return '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">Active</span>';
            });
    }

    /**
     * PowerGrid column setup and properties set up
     */
    public function columns(): array
    {
        return [
            // Display for user ID
            Column::make('User id', 'user_id')
                ->hidden(),

            // Display for status of data (active or soft-deleted)
            Column::make('Status', 'status_badge')
                ->visibleInExport(false)
                ->bodyAttribute('text-center'),
            // Hidden column for status of data (active or soft-deleted), used for export and filtering
            Column::make('Status', 'status')
                ->hidden()
                ->bodyAttribute('text-sm')
                ->visibleInExport(true),

            // Personal information display
            Column::make('First name', 'first_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),
            Column::make('Middle name', 'middle_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),
            Column::make('Last name', 'last_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            // Branch assignment and department display
            Column::make('Branch', 'branch')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),
            Column::make('Department', 'department')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            // Academic rank/status display
            Column::make('Academic rank', 'academic_rank')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            // Contact information display
            Column::make('Email', 'email')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),
            Column::make('Contactno', 'contactno')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),
            Column::make('Address', 'address')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            // Sex and birthday display
            Column::make('Sex', 'sex')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),
            Column::make('Birthday', 'birthday_formatted', 'birthday')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->hidden(isHidden: true, isForceHidden: false),

            // Actions column
            Column::action('Action'),
        ];
    }

    /**
     * PowerGrid filters list set up
     */
    public function filters(): array
    {
        return [
            Filter::select('branch', 'branch')
                ->dataSource(FacultyProfile::select('branch')->distinct()->get())
                ->optionLabel('branch')
                ->optionValue('branch'),

            Filter::select('department', 'department')
                ->dataSource(FacultyProfile::select('department')->distinct()->get())
                ->optionLabel('department')
                ->optionValue('department'),
        ];
    }

    /**
     * Display action buttons for each row
     */
    public function actions(FacultyProfile $row): array
    {
        // Actions for data that is soft-deleted (in trash)
        if ($row->trashed()) {
            return [
                // Restore data button
                Button::add('restore')
                    ->icon('default-arrow-path', ['class' => 'w-5 h-5 text-amber-500 group-hover:text-amber-700 transition duration-150'])
                    ->class('group cursor-pointer')
                    ->attributes(['title' => 'Restore Data'])
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
            // View details button
            Button::add('view')
                ->icon('default-eye', ['class' => 'w-5 h-5 text-green-500 group-hover:text-green-700 transition duration-150'])
                ->class('group cursor-pointer')
                ->attributes(['title' => 'View Faculty'])
                ->dispatch('navigate-to-view', ['id' => $row->id]),

            // Move to Trash button
            Button::add('delete')
                ->icon('default-trash', ['class' => 'w-5 h-5 text-red-500 group-hover:text-red-700 transition duration-150'])
                ->class('group cursor-pointer')
                ->attributes(['title' => 'Delete Faculty'])
                ->dispatch('confirm-delete', ['id' => $row->id]),
        ];
    }

    /**
     * EVENTS & LISTENERS
     */
    #[On('navigate-to-view')]
    public function navigateToView(int $id)
    {
        return $this->redirectRoute('admin.faculty.show', ['faculty' => $id], navigate: true);
    }

    #[On('confirm-delete')]
    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        Flux::modal('delete-confirmation')->show();
    }

    #[On('open-bulk-delete-modal')]
    public function openBulkDeleteModal()
    {
        if (count($this->checkboxValues) > 0) {
            Flux::modal('bulk-delete-confirmation')->show();
        }
    }

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
        $faculty = FacultyProfile::withTrashed()->find($id);

        if ($faculty) {
            $faculty->restore();

            // Also Restore the User Account
            if ($faculty->user_id) {
                User::withTrashed()->find($faculty->user_id)?->restore();
            }

            $this->toast()
                ->info('Restored', 'Faculty and User account restored successfully.')
                ->send();
        }
    }

    // Execute restore selected data from trash
    #[On('bulk-restore')]
    public function bulkRestore(): void
    {
        $count = count($this->checkboxValues);

        if ($count === 0) {
            $this->toast()->warning('Warning', 'No records selected.')->send();

            return;
        }

        $ids = $this->checkboxValues;

        // Fetch soft-deleted profiles
        $faculties = FacultyProfile::onlyTrashed()->whereIn('id', $ids)->get();

        foreach ($faculties as $faculty) {
            $faculty->restore();

            // Restore associated User
            if ($faculty->user_id) {
                User::withTrashed()->find($faculty->user_id)?->restore();
            }
        }

        $this->checkboxValues = [];

        $this->toast()->success('Success', "$count records restored successfully.")->send();
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

        $ids = $this->checkboxValues;

        // Fetch profiles including trashed to determine action type
        $faculties = FacultyProfile::withTrashed()->whereIn('id', $ids)->get();

        foreach ($faculties as $faculty) {
            $user = null;
            if ($faculty->user_id) {
                $user = User::withTrashed()->find($faculty->user_id);
            }

            if ($faculty->trashed()) {
                // Action: Force Delete (Permanent)
                $user?->forceDelete();
                $faculty->forceDelete();
            } else {
                // Action: Soft Delete (Trash)
                $user?->delete();
                $faculty->delete();
            }
        }

        $this->checkboxValues = [];

        Flux::modal('bulk-delete-confirmation')->close();

        $this->toast()->success('Success', "$count records deleted successfully.")->send();
    }

    // Execute soft delete (move to trash)
    #[On('delete')]
    public function destroy(): void
    {
        if ($this->deleteId) {
            $faculty = FacultyProfile::find($this->deleteId);

            if ($faculty) {
                // Soft Delete User Account
                if ($faculty->user_id) {
                    User::find($faculty->user_id)?->delete();
                }

                // Soft Delete Faculty Profile
                $faculty->delete();
            }

            $this->deleteId = null;
            Flux::modal('delete-confirmation')->close();

            $this->toast()
                ->success('Removed', 'Faculty and User account moved to trash.')
                ->send();
        }
    }

    // Execute permanent delete of data
    #[On('force-delete')]
    public function forceDestroy(): void
    {
        if ($this->forceDeleteId) {
            $faculty = FacultyProfile::withTrashed()->find($this->forceDeleteId);

            if ($faculty) {
                // Permanently Delete User Account
                if ($faculty->user_id) {
                    User::withTrashed()->find($faculty->user_id)?->forceDelete();
                }

                // Permanently Delete Faculty Profile
                $faculty->forceDelete();
            }

            $this->forceDeleteId = null;
            Flux::modal('force-delete-confirmation')->close();

            $this->toast()
                ->success('Removed', 'Faculty and User account permanently deleted.')
                ->send();
        }
    }
}
