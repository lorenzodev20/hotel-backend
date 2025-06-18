<?php

namespace Database\Seeders;

use App\Models\AccommodationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccommodationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $path = base_path('database/seeders/data/accommodation_types.csv');

            if (!File::exists($path)) {
                $this->command->error("El archivo CSV no existe en $path");
                return;
            }

            $lines = array_map('str_getcsv', file($path));

            // Opcional: eliminar el encabezado
            $header = array_shift($lines);

            foreach ($lines as $line) {
                $name = trim($line[0]);
                AccommodationType::firstOrCreate([
                    "name" => $name
                ]);
            }

            $this->command->info("Se insertaron los datos correctamente.");

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('AccommodationTypeSeeder error - ' . $th->getMessage());
        }
    }
}
