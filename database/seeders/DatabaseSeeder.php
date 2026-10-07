<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(SolutionIndustriesSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(GeneralFaqsSeeder::class);
        $this->call(SolutionsSeeder::class);
        $this->call(HomeSectionsSeeder::class);
        $this->call(HomeContentSeeder::class);
        $this->call(PagesSeeder::class);
        $this->call(SeoRouteSeeder::class);
        $this->call(TechnologiesSeeder::class);

        if (! User::where('email', 'admin@bitcodak.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@bitcodak.com',
            ])->assignRole('Administrator');
        }
    }
}
