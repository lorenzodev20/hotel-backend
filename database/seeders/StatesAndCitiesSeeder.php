<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class StatesAndCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryId = 43;
        try {
            DB::beginTransaction();

            DB::table('countries')->updateOrInsert([
                'id' => $countryId,
                'name'  => "COLOMBIA",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $jsonPath = database_path('seeders\data\departments.json');
            $departments = json_decode(File::get($jsonPath), true);

            foreach ($departments as $department) {
                $stateId = DB::table('states')->insertGetId([
                    'name' => $department['departamento'],
                    'country_id' => $countryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($department['ciudades'] as $city) {
                    DB::table('cities')->insert([
                        'name' => $city,
                        'state_id' => $stateId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('StatesAndCitiesSeeder error - '.$th->getMessage());
        }
    }
}
