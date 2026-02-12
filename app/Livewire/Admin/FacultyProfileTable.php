<?php

namespace App\Livewire\Admin;

use App\Models\FacultyProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class FacultyProfileTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'facultyProfileTable';

    public bool $showFilters = false;

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
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return FacultyProfile::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->role('faculty');
            });
    }

    public function template(): ?string
    {
        return \App\Livewire\PowerGridTheme::class;
    }

    // Search with relationship
    public function relationSearch(): array
    {
        return [
            'user' => [
                'name',
            ],
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
            ->add('birthday_formatted', fn($model) => Carbon::parse($model->birthday)->format('d/m/Y'))
            ->add('updated_by')
            ->add('created_at_formatted', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'))
            ->add('updated_at_formatted', fn($model) => Carbon::parse($model->updated_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('User id', 'user_id')
                ->hidden(),

            Column::make('First name', 'first_name')
                ->bodyAttribute('text-sm')
                ->sortable()
                ->searchable(),

            Column::make('Middle name', 'middle_name')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Last name', 'last_name')
                ->sortable()
                ->searchable(),

            Column::make('Branch', 'branch')
                ->sortable()
                ->searchable(),

            Column::make('Department', 'department')
                ->sortable()
                ->searchable(),

            Column::make('Academic rank', 'academic_rank')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Contactno', 'contactno')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Address', 'address')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Birthday', 'birthday_formatted', 'birthday')
                ->sortable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Updated by', 'updated_by')
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable()
                ->hidden(isHidden: true, isForceHidden: false),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable()
                ->hidden(isHidden: true, isForceHidden: false),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('branch')
                ->dataSource(FacultyProfile::select('branch')->distinct()->get())
                ->optionLabel('branch')
                ->optionValue('branch'),

            Filter::select('department')
                ->dataSource(FacultyProfile::select('department')->distinct()->get())
                ->optionLabel('department')
                ->optionValue('department'),

            Filter::select('academic_rank')
                ->dataSource(FacultyProfile::select('academic_rank')->distinct()->get())
                ->optionLabel('academic_rank')
                ->optionValue('academic_rank'),

            Filter::select('sex')
                ->dataSource(FacultyProfile::select('sex')->distinct()->get())
                ->optionLabel('sex')
                ->optionValue('sex'),

            // Filter::datetimepicker('birthday'),
            // Filter::datetimepicker('created_at'),
            // Filter::datetimepicker('updated_at'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: ' . $row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

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
