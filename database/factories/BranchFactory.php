<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['Main', 'Satellite']);

        return [
            'branch_id' => Branch::generateNextId($type),
            'code' => strtoupper($this->faker->unique()->lexify('????')),
            'name' => 'CvSU '.$type.' - '.$this->faker->city,
            'type' => $type,
            'address' => $this->faker->address,
            'is_active' => true,
        ];
    }
}
