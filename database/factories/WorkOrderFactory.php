<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory {
    public function definition(): array {
        return [
            'code'=> strtoupper($this->faker->bothify('WO###')),
            'title'=> $this->faker->sentence(3),
            'status'=> $this->faker->randomElement(['open','in_progress','done']),
            'customer_id'=> Customer::factory(),
        ];
    }
}
