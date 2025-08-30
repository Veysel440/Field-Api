<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory {
    public function definition(): array {
        $lat = $this->faker->latitude(36, 42);
        $lng = $this->faker->longitude(26, 45);
        return [
            'code'=> strtoupper($this->faker->bothify('AS###')),
            'name'=> $this->faker->streetName(),
            'customer_id'=> Customer::factory(),
            'lat'=>$lat, 'lng'=>$lng,
        ];
    }
}
