<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        if (Schema::hasTable('disposable_settings')) {
            // OpenAIP API KEY for maps
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.openaip_api_key'],
                ['group' => 'API Service', 'name' => 'OpenAIP API Key (for Map Overlays)', 'field_type' => 'text', 'default' => null, 'order' => '9911']
            );
        }
    }
};
