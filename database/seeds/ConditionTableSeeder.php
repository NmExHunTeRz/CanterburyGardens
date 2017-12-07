<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conditions')
            ->insert(
                ['site_id' => 'gh1', 'high_humidity' => 40, 'low_humidity' => 0, 'high_moisture' => 50, 'low_moisture' => 5, 'high_lux' => 50000, 'low_lux' => 20000, 'high_temp' => 29, 'low_temp' => 7, 'winter_high_temp' => 10, 'winter_low_temp' => 8, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'gh2', 'high_humidity' => 60, 'low_humidity' => 30, 'high_moisture' => 70, 'low_moisture' => 10, 'high_lux' => 20000, 'low_lux' => 10000, 'high_temp' => 18, 'low_temp' => 7, 'winter_high_temp' => 18, 'winter_low_temp' => null, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'gh3', 'high_humidity' => 60, 'low_humidity' => 30, 'high_moisture' => 50, 'low_moisture' => 30, 'high_lux' => 50000, 'low_lux' => 10000, 'high_temp' => 26, 'low_temp' => 10, 'winter_high_temp' => 26, 'winter_low_temp' => null, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'field', 'high_humidity' => 40, 'low_humidity' => 10, 'high_moisture' => 70, 'low_moisture' => 30, 'high_lux' => 50000, 'low_lux' => 10000, 'high_temp' => 23, 'low_temp' => 10, 'winter_high_temp' => 23, 'winter_low_temp' => null, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'shed', 'high_humidity' => 20, 'low_humidity' => 0, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => 10000, 'low_lux' => 5, 'high_temp' => 15, 'low_temp' => 10, 'winter_high_temp' => 10, 'winter_low_temp' => null, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'muck_heap', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => null, 'low_lux' => null, 'high_temp' => 43, 'low_temp' => 37, 'winter_high_temp' => 30, 'winter_low_temp' => 37, 'gas' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }

}
