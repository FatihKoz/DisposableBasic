<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        if (Schema::hasTable('disposable_settings')) {
            // Carto API KEY for maps
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.carto_api_key'],
                ['group' => 'API Service', 'name' => 'Carto API Key (for Maps)', 'field_type' => 'text', 'default' => null, 'order' => '9910']
            );
        }
    }
};
