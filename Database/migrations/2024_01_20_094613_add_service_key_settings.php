<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        if (Schema::hasTable('disposable_settings')) {
            // Settings for API Service Key
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.srvkey'],
                ['group' => 'API Service', 'name' => 'Service Key (API)', 'field_type' => 'text', 'default' => null, 'order' => '9901']
            );
            // Settings for Event Widget and API Events
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.event_routecode'],
                ['group' => 'API Service', 'name' => 'Event Route Code (max 5 chars)', 'field_type' => 'text', 'default' => 'EVENT', 'order' => '9902']
            );
        }
    }
};
