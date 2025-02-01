<?php

namespace Database\Factories;

use App\Models\GeneratedBill;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneratedBillFactory extends Factory
{
    protected $model = GeneratedBill::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'generated_date' => $this->faker->date(),
            'bill_type' => $this->faker->randomElement(['monthly', 'one_time']),
            'remarks' => $this->faker->sentence(),
            'month' => $this->faker->randomElement(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'other']), // Random month
            'client_id' => Client::inRandomOrder()->first()->id,
            'created_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
