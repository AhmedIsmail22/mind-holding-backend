<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SolutionIndustryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => ['ar' => $name, 'en' => $name],
            'slug' => str()->slug($name).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'order' => 0,
        ];
    }
}
