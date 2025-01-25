<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        // Add New Settings for Auto Reject
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_aircraft_icao'],
                ['group' => 'Auto Reject', 'name' => 'Aircraft ICAO Type', 'field_type' => 'check', 'default' => 'false', 'order' => '8912']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_livery'],
                ['group' => 'Auto Reject', 'name' => 'Aircraft Livery', 'field_type' => 'check', 'default' => 'false', 'order' => '8913']
            );
        }
    }
};
