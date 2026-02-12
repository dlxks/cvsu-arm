<?php

namespace App\Livewire\Admin;

use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive;
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
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),

            PowerGrid::responsive()
                ->fixedColumns('first_name', 'last_name', Responsive::ACTIONS_COLUMN_NAME),
        ];
    }

    public function datasource(): Builder
    {
        // Optimized eager loading
        return FacultyProfile::query()->with('user');
    }

    public function relationSearch(): array
    {
        return [
            'user' => ['name', 'email'],
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
            ->add('birthday_formatted', fn(FacultyProfile $model) => $model->birthday ? Carbon::parse($model->birthday)->format('d/m/Y') : '-');
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

            Column::action('Action')
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

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js("alert('Editing ID: ' + $rowId)");
    }

    #[\Livewire\Attributes\On('singleDelete')]
    public function singleDelete($rowId): void
    {
        $this->checkboxValues = [$rowId];
        $this->bulkDelete();
    }

    #[\Livewire\Attributes\On('bulkDelete')]
    public function bulkDelete(): void
    {
        $ids = $this->checkboxValues;

        if (count($ids) === 0) {
            $this->dispatch(
                'toast',
                text: 'Please select at least one record to delete.',
                variant: 'error'
            );
            return;
        }

        try {
            DB::transaction(function () use ($ids) {
                $profiles = FacultyProfile::whereIn('id', $ids)->get();
                $userIds = $profiles->pluck('user_id')->filter();

                // Delete profiles first
                FacultyProfile::whereIn('id', $ids)->delete();

                // Delete associated users
                if ($userIds->isNotEmpty()) {
                    User::whereIn('id', $userIds)->delete();
                }
            });

            $this->clearCheckBox();

            $this->dispatch(
                'toast',
                text: count($ids) . ' records deleted successfully.',
                heading: 'Success',
                variant: 'success'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                text: 'An error occurred while deleting records.',
                variant: 'error'
            );
        }
    }

    public function actions(FacultyProfile $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600')
                ->dispatch('edit', ['rowId' => $row->id]),

            // Individual delete button for a single row
            Button::add('delete')
                ->slot('Delete')
                ->id()
                ->class('text-red-500 hover:text-red-700')
                ->dispatch('singleDelete', ['rowId' => $row->id])
        ];
    }
}
