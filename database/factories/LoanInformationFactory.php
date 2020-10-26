<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LoanInformation;

class LoanInformationFactory extends Factory
{
    protected $model = LoanInformation::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => 'Loan description',
            'amount' => 1000,
            'period' => 12,
            'rate_per_week' => 0.01
        ];
    }
}