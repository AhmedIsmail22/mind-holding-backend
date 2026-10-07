<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $client = $this->faker->company();
        $slug = str()->slug($client).'-'.$this->faker->unique()->numberBetween(1, 100000);

        return [
            'client_name' => ['ar' => $client, 'en' => $client],
            'slug' => $slug,
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
