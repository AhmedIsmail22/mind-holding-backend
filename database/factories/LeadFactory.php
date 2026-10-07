<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'quote',
            'status' => 'new',
            'name' => $this->faker->name(),
            'mobile' => '+201115646730',
            'email' => $this->faker->safeEmail(),
            'language' => 'en',
            'page_url' => 'https://bitcodak.com/en/contact',
            'utm' => null,
        ];
    }
}
