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
                ['site_id' => 'gh1', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => 50000, 'low_lux' => 200, 'high_temp' => 29, 'low_temp' => 7, 'winter_high_temp' => 10, 'winter_low_temp' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'gh2', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => null, 'low_lux' => null, 'high_temp' => null, 'low_temp' => null, 'winter_high_temp' => null, 'winter_low_temp' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'gh3', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => null, 'low_lux' => null, 'high_temp' => null, 'low_temp' => null, 'winter_high_temp' => null, 'winter_low_temp' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'outside', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => null, 'low_lux' => null, 'high_temp' => null, 'low_temp' => null, 'winter_high_temp' => null, 'winter_low_temp' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('conditions')
            ->insert(
                ['site_id' => 'house', 'high_humidity' => null, 'low_humidity' => null, 'high_moisture' => null, 'low_moisture' => null, 'high_lux' => null, 'low_lux' => null, 'high_temp' => null, 'low_temp' => null, 'winter_high_temp' => null, 'winter_low_temp' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }

}
