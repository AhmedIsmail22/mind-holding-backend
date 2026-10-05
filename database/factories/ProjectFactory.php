<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $client = $this->faker->company();

        return [
            'client_name' => ['ar' => $client, 'en' => $client],
            'hide_client_name' => false,
            'generic_description' => null,
            'overview' => ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'challenge' => ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'solution' => ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'technologies' => ['Laravel', 'React'],
            'live_url' => null,
            'is_published' => true,
            'order' => 0,
        ];
    }
}
