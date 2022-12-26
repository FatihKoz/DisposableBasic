<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        // Add New Settings for Auto Reject and Update older ones
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginthrdist'],
                ['group' => 'Auto Reject', 'name' => 'Threshold Distance (ft)', 'field_type' => 'numeric', 'default' => '0', 'order' => '8908']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_margingforce'],
                ['group' => 'Auto Reject', 'name' => 'G-Force', 'field_type' => 'numeric', 'default' => '0', 'order' => '8909']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginfburn'],
                ['group' => 'Auto Reject', 'name' => 'Fuel Burn (lbs)', 'field_type' => 'numeric', 'default' => '0', 'order' => '8906']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginlrate'],
                ['group' => 'Auto Reject', 'name' => 'Landing Rate (-ft/min)', 'field_type' => 'numeric', 'default' => '0', 'order' => '8907']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginftime'],
                ['group' => 'Auto Reject', 'name' => 'Flight Time (min)', 'field_type' => 'numeric', 'default' => '0', 'order' => '8905']
            );
        }
    }
};
