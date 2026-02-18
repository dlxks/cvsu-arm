<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'branch_code' => strtoupper($this->faker->unique()->lexify('BR???')),
            'branch_name' => $this->faker->company(),
            'branch_location' => $this->faker->city(),
            'updated_by' => User::inRandomOrder()->first()?->id ?? null,
        ];
    }
}
