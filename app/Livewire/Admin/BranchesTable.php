<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
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

final class BranchesTable extends PowerGridComponent
{
    use Interactions, WithExport;

    public string $tableName = 'branchesTable';

    // Default sorting
    public string $sortField = 'code';

    public string $sortDirection = 'asc';

    // State for deletion
    public ?int $deleteId = null;

    // State for force deletion
    public ?int $forceDeleteId = null;

    public string $softDeletes = 'withTrashed';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'branch-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true),

            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),

            PowerGrid::responsive()
                ->fixedColumns('code', 'name', Responsive::ACTIONS_COLUMN_NAME),
        ];
    }

    public function datasource(): Builder
    {
        return Branch::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('type')
            ->add('code')
            ->add('name')
            ->add('address')
            ->add('created_at_formatted', fn (Branch $model) => $model->created_at->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->bodyAttribute('text-sm')
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Type', 'type')
                ->sortable()
                ->bodyAttribute('text-sm')
                ->searchable(),

            Column::make('Code', 'code')
                ->sortable()
                ->bodyAttribute('text-sm')
                ->searchable(),

            Column::make('Name', 'name')
                ->sortable()
                ->bodyAttribute('text-sm')
                ->searchable(),

            Column::make('Address', 'address')
                ->sortable()
                ->bodyAttribute('text-sm')
                ->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datetimepicker('created_at'),
            Filter::datetimepicker('updated_at'),
            Filter::datetimepicker('deleted_at'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Branch $row): array
    {
        return [

            Button::add('edit')
                ->icon('default-pencil-square', [
                    'class' => 'w-5 h-5 text-blue-500 group-hover:text-blue-700 transition duration-150',
                ])
                ->class('group cursor-pointer')
                ->attributes([
                    'title' => 'Edit Branch',
                ])
                ->dispatch('navigate-to-edit', ['id' => $row->id]),

            Button::add('delete')
                ->icon('default-trash', ['class' => 'w-5 h-5 text-red-500 group-hover:text-red-700 transition duration-150'])
                ->class('group cursor-pointer')
                ->attributes(['title' => 'Delete Branch'])
                ->dispatch('confirm-delete', ['id' => $row->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
