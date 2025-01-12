<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        if (Schema::hasTable('disposable_settings')) {
            // Add News message settings
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_newsmsg'],
                ['group' => 'Discord', 'name' => 'Send News Message', 'default' => 'false', 'field_type' => 'check', 'order' => '2004']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_news_msgposter'],
                ['group' => 'Discord', 'name' => 'Message Poster (News)', 'default' => config('app.name'), 'order' => '2005']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_news_webhook'],
                ['group' => 'Discord', 'name' => 'Webhook URL (News)', 'order' => '2006']
            );
            // Update old Pirep message settings
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_pirepmsg'],
                ['group' => 'Discord', 'name' => 'Send Pirep Filed Message', 'default' => 'false', 'field_type' => 'check', 'order' => '2001']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_pirep_msgposter'],
                ['group' => 'Discord', 'name' => 'Message Poster (Pirep)', 'default' => config('app.name'), 'order' => '2002']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.discord_pirep_webhook'],
                ['group' => 'Discord', 'name' => 'Webhook URL (Pirep)', 'order' => '2003']
            );
        }
    }
};
