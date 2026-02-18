<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;

class BranchesImport implements ToModel
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Branch([
            'type' => strtoupper($row['type'] ?? 'EXTENSION'),
            'code' => strtoupper($row['code']),
            'name' => $row['name'],
            'address' => $row['address'] ?? null,
        ]);
    }
}
