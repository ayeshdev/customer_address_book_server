<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'customer_id' => Customer::factory(), // Factory relationship to Customer
            'no' => $this->faker->buildingNumber,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
        ];
    }
}
