<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        // Add New Settings for WhazzUp Checks (Network Presence)
        if (Schema::hasTable('disposable_settings')) {
            // Add New Settings
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_enroute_margin'],
                ['group' => 'Network Checks', 'name' => 'ENROUTE Checks Margin (Seconds)', 'field_type' => 'numeric', 'default' => '300', 'order' => '8820']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_cron'],
                ['group' => 'Network Checks', 'name' => 'Download data with CRON', 'field_type' => 'check', 'default' => 'false', 'order' => '8830']
            );
            // Delete 'AUTO' whazzup data (which in fact is IVAO mislabeled)
            DB::table('disposable_whazzup')->where('network', 'AUTO')->delete();
        }
    }
};
