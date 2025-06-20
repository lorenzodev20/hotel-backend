<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StatesAndCitiesSeeder::class);
        $this->call(AccommodationTypeSeeder::class);
        $this->call(RoomTypeSeeder::class);
        $this->call(RoomRulesSeeder::class);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@panel.com',
            'password' => Hash::make('esTheAdmin.25*')
        ]);
    }
}
