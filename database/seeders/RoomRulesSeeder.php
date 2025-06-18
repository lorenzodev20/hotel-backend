<?php

namespace Database\Seeders;

use App\Models\RoomRule;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use App\Models\AccommodationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/room_accommodation_rules.json');
        $rules = json_decode(File::get($jsonPath), true);

        DB::table('room_rules')->truncate();

        foreach ($rules as $roomType => $accommodations) {
            $roomTypeDb = RoomType::where('name', $roomType)->first();
            if ($roomType) {
                foreach ($accommodations as $value) {
                    $accommodationType = AccommodationType::where('name', $value)->first();
                    if ($accommodationType) {
                        $obj = new RoomRule();
                        $obj->enabled = true;
                        $obj->roomType()->associate($roomTypeDb);
                        $obj->accommodationType()->associate($accommodationType);
                        $obj->save();
                    }
                }
            }
        }
    }
}
