<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $type = $row['type'] ?? 'Main';

        return new Branch([
            // Use branch_id instead of id based on your migration
            'branch_id' => $row['branch_id'] ?? Branch::generateNextId($type),
            'code' => $row['code'],
            'name' => $row['name'],
            'type' => $type,
            'address' => $row['address'] ?? null,
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            // Ensure we validate code and branch_id against the correct columns
            'code' => ['required', 'string', 'unique:branches,code'],
            'name' => ['required', 'string'],
            'type' => ['required', 'in:Main,Satellite'],
            'branch_id' => ['nullable', 'string', 'unique:branches,branch_id'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function customValidationMessages()
    {
        return [
            'code.unique' => 'The branch code :input has already been taken.',
            'branch_id.unique' => 'The Branch ID :input has already been taken.',
            'type.in' => 'The type must be either Main or Satellite.',
        ];
    }
}
