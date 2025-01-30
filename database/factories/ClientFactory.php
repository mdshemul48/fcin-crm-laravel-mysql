<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

class ClientFactory extends Factory
{
    use WithFaker;

    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => $this->faker->unique()->randomNumber(),
            'username' => $this->faker->userName,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'package_id' => Package::factory(),
            'bill_amount' => $this->faker->randomFloat(2, 100, 1000),
            'due_amount' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['paid', 'due']),
            'billing_status' => $this->faker->randomElement([true, false]),
            'remarks' => $this->faker->sentence,
            'created_by' => User::factory(),
        ];
    }
}
