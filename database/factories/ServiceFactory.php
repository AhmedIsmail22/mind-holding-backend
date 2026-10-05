<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'group' => $this->faker->randomElement(['software', 'marketing']),
            'name' => ['ar' => $name, 'en' => $name],
            'slug' => str()->slug($name).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'description' => ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'deliverables' => [
                ['ar' => $this->faker->sentence(3), 'en' => $this->faker->sentence(3)],
            ],
            'delivery_steps' => [
                ['ar' => $this->faker->sentence(3), 'en' => $this->faker->sentence(3)],
            ],
            'is_published' => true,
            'is_draft' => false,
            'order' => 0,
        ];
    }
}
