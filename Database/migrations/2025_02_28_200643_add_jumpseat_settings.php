<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        // Add Settings for Jumpseat
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_price_type'],
                ['group' => 'Jumpseat', 'name' => 'Type', 'field_type' => 'select', 'options' => 'auto,free,fixed', 'default' => 'auto', 'order' => '8801']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_fixed_price'],
                ['group' => 'Jumpseat', 'name' => 'Fixed Price', 'field_type' => 'numeric', 'default' => '0', 'order' => '8802']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_base_price'],
                ['group' => 'Jumpseat', 'name' => 'Auto Price Base Fee per nm', 'field_type' => 'decimal', 'default' => '0.13', 'order' => '8803']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_free_dates'],
                ['group' => 'Jumpseat', 'name' => 'Free Dates (month and day like 1231)', 'field_type' => 'text', 'default' => '0101,1231', 'order' => '8804']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_hubs_only'],
                ['group' => 'Jumpseat', 'name' => 'Hubs Only', 'field_type' => 'check', 'default' => 'false', 'order' => '8805']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_auto_request'],
                ['group' => 'Jumpseat', 'name' => 'Auto Request', 'field_type' => 'check', 'default' => 'true', 'order' => '8901']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.js_pilot_request'],
                ['group' => 'Jumpseat', 'name' => 'Manual Request', 'field_type' => 'check', 'default' => 'false', 'order' => '8902']
            );
        }
    }
};
