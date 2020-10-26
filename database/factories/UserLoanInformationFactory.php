<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserLoanInformation;

class UserLoanInformationFactory extends Factory
{
    protected $model = UserLoanInformation::class;

    public function definition()
    {
        return [
            'amount_per_week' => 12,
            'paid_amount' => 0
        ];
    }
}