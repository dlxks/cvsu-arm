<?php

namespace App\Livewire\Admin;

use App\Models\FacultyProfile;
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

final class FacultyProfileTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'facultyProfileTable';

    // Default sorting
    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    // State for deletion
    public ?int $deleteId = null;

    // State for force deletion
    public ?int $forceDeleteId = null;

    /* -----------------------------------------------------------------
           CONFIGURATION
    ----------------------------------------------------------------- */
    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'faculty-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true)
                ->includeViewOnTop('livewire.admin.faculty.header-actions'),

            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),

            PowerGrid::responsive()
                ->fixedColumns('first_name', 'last_name', Responsive::ACTIONS_COLUMN_NAME),

        ];
    }

    /* -----------------------------------------------------------------
           DATA SOURCE & FIELDS
    ----------------------------------------------------------------- */

    public function datasource(): Builder
    {
        return FacultyProfile::query()->with('user');
    }

    public function relationSearch(): array
    {
        return [
            'user' => ['name', 'email'],
        ];
    }

    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Delete Selected (<span x-text="window.pgBulkActions.count(\''.$this->tableName.'\')"></span>)')
                ->attributes([
                    'x-show' => 'window.pgBulkActions.count(\''.$this->tableName.'\') > 0',
                    'x-cloak' => true,
                    'class' => 'px-2 py-2 text-sm border border-red-500 bg-red-400 text-white rounded-md cursor-pointer',
                ])
                ->dispatch('open-bulk-delete-modal', []),
        ];
    }

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
            ->add('status', fn (FacultyProfile $model) => $model->trashed()
                ? '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">Deleted</span>'
                : '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">Active</span>'
            );
    }

    public function columns(): array
    {
        return [
            Column::make('User id', 'user_id')
                ->hidden(),

            Column::make('Status', 'status')
                ->bodyAttribute('text-center'),

            Column::make('First name', 'first_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            Column::make('Middle name', 'middle_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Last name', 'last_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            Column::make('Branch', 'branch')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            Column::make('Department', 'department')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            Column::make('Academic rank', 'academic_rank')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

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

            Column::make('Sex', 'sex')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Birthday', 'birthday_formatted', 'birthday')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::action('Action'),
        ];
    }

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

    /* -----------------------------------------------------------------
       ROW ACTIONS
    ----------------------------------------------------------------- */
    public function actions(FacultyProfile $row): array
    {
        // 1. Check if the row is soft-deleted
        if ($row->trashed()) {
            return [
                Button::add('restore')
                    ->icon('default-arrow-path', [
                        'class' => 'w-5 h-5 text-amber-500 group-hover:text-amber-700 transition duration-150',
                    ])
                    ->class('group cursor-pointer')
                    ->attributes(['title' => 'Restore Data'])
                    ->dispatch('restore', ['id' => $row->id]),

                Button::add('force-delete')
                    ->icon('default-trash', [
                        'class' => 'w-5 h-5 text-red-600 group-hover:text-red-800 transition duration-150',
                    ])
                    ->class('group cursor-pointer')
                    ->attributes(['title' => 'Delete Permanently'])
                    ->dispatch('confirm-force-delete', ['id' => $row->id]),
            ];
        }

        // 2. Standard buttons for active rows
        return [
            Button::add('view')
                ->icon('default-eye', [
                    'class' => 'w-5 h-5 text-green-500 group-hover:text-green-700 transition duration-150',
                ])
                ->class('group cursor-pointer')
                ->attributes([
                    'title' => 'View Faculty',
                ])
                ->dispatch('navigate-to-view', ['id' => $row->id]),

            Button::add('edit')
                ->icon('default-pencil-square', [
                    'class' => 'w-5 h-5 text-blue-500 group-hover:text-blue-700 transition duration-150',
                ])
                ->class('group cursor-pointer')
                ->attributes([
                    'title' => 'Edit Faculty',
                ])
                ->dispatch('navigate-to-edit', ['id' => $row->id]),

            Button::add('delete')
                ->icon('default-trash', [
                    'class' => 'w-5 h-5 text-red-500 group-hover:text-red-700 transition duration-150',
                ])
                ->class('group cursor-pointer')
                ->attributes([
                    'title' => 'Delete Faculty',
                ])
                ->dispatch('confirm-delete', ['id' => $row->id]),
        ];
    }

    /* -----------------------------------------------------------------
       EVENTS & LISTENERS
    ----------------------------------------------------------------- */

    #[On('navigate-to-edit')]
    public function navigateToEdit(int $id)
    {
        return $this->redirectRoute('admin.faculty.edit', ['faculty' => $id], navigate: true);
    }

    #[On('navigate-to-view')]
    public function navigateToView(int $id)
    {
        return $this->redirectRoute('admin.faculty.show', ['faculty' => $id], navigate: true);
    }

    #[On('confirm-delete')]
    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->js('$flux.modal("delete-confirmation").show()');
    }

    #[On('open-bulk-delete-modal')]
    public function openBulkDeleteModal()
    {
        if (count($this->checkboxValues) > 0) {
            $this->js('$flux.modal("bulk-delete-confirmation").show()');
        }
    }

    // -- NEW HANDLERS FOR RESTORE & FORCE DELETE --

    #[On('restore')]
    public function restore(int $id)
    {
        $record = FacultyProfile::withTrashed()->find($id);
        if ($record) {
            $record->restore();
            // Optional: flux toast
            // $this->js('Flux.toast("Record restored successfully.")');
        }
    }

    #[On('confirm-force-delete')]
    public function confirmForceDelete(int $id)
    {
        $this->forceDeleteId = $id;
        // Make sure you have a <flux:modal name="force-delete-confirmation"> in your view
        $this->js('$flux.modal("force-delete-confirmation").show()');
    }

    /* -----------------------------------------------------------------
       EXECUTION LOGIC
    ----------------------------------------------------------------- */

    public function destroy(): void
    {
        if ($this->deleteId) {
            FacultyProfile::find($this->deleteId)->delete();
            $this->deleteId = null;
            $this->js('$flux.modal("delete-confirmation").close()');
        }
    }

    public function forceDestroy(): void
    {
        if ($this->forceDeleteId) {
            FacultyProfile::withTrashed()->find($this->forceDeleteId)->forceDelete();
            $this->forceDeleteId = null;
            $this->js('$flux.modal("force-delete-confirmation").close()');
            // Optional: flux toast
            // $this->js('Flux.toast("Record permanently deleted.")');
        }
    }

    public function bulkDestroy(): void
    {
        if (count($this->checkboxValues) > 0) {
            FacultyProfile::whereIn('id', $this->checkboxValues)->delete();

            $this->checkboxValues = []; // Clear selection
            $this->js('$flux.modal("bulk-delete-confirmation").close()');
        }
    }
}
