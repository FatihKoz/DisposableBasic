<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HandleDisposableSettingsTable extends Migration
{
    public function up()
    {
        // Create Disposable Settings Table
        if (!Schema::hasTable('disposable_settings')) {
            Schema::create('disposable_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 200)->nullable();
                $table->string('key', 100);
                $table->string('value', 500)->nullable();
                $table->string('group', 100)->nullable();
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
                $table->unique('key');
            });
        }

        // Update Disposable Settings Table
        if (Schema::hasTable('disposable_settings') && !Schema::hasColumn('disposable_settings', 'field_type')) {
            Schema::table('disposable_settings', function (Blueprint $table) {
                $table->string('default', 250)->nullable()->after('value');
                $table->string('field_type', 50)->nullable()->after('group');
                $table->text('options')->nullable()->after('field_type');
                $table->string('desc', 250)->nullable()->after('options');
                $table->string('order', 6)->nullable()->after('desc');
            });
        }

        // Update Settings (Airlines and Tools)
        if (Schema::hasTable('disposable_settings')) {
            // Disposable Airlines settings
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dairlines.acstate_control'],
                ['key' => 'dbasic.acstate_control', 'group' => 'Aircraft', 'name' => 'Aircraft State Control', 'default' => 'false', 'field_type' => 'check', 'order' => '1001']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dairlines.discord_pirepmsg'],
                ['key' => 'dbasic.discord_pirepmsg', 'group' => 'Discord', 'name' => 'Pirep Filed Message', 'default' => 'false', 'field_type' => 'check', 'order' => '2001']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dairlines.discord_pirep_msgposter'],
                ['key' => 'dbasic.discord_pirep_msgposter', 'group' => 'Discord', 'name' => 'Message Poster', 'default' => config('app.name'), 'order' => '2002']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dairlines.discord_pirep_webhook'],
                ['key' => 'dbasic.discord_pirep_webhook', 'group' => 'Discord', 'name' => 'Webhook URL (Pirep)', 'order' => '2003']
            );
            // WhazzUp Widget (IVAO)
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dtools.whazzup_ivao_fieldname'],
                ['key' => 'dbasic.whazzup_ivao_fieldname', 'group' => 'IVAO', 'name' => 'IVAO ID Field Name', 'default' => 'IVAO', 'order' => '1011']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dtools.whazzup_ivao_refresh'],
                ['key' => 'dbasic.whazzup_ivao_refresh', 'group' => 'IVAO', 'name' => 'Data Refresh Rate (secs)', 'field_type' => 'numeric', 'default' => '60', 'order' => '1012']
            );
            // WhazzUp Widget (VATSIM)
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dtools.whazzup_vatsim_fieldname'],
                ['key' => 'dbasic.whazzup_vatsim_fieldname', 'group' => 'VATSIM', 'name' => 'VATSIM CID Field Name', 'default' => 'VATSIM', 'order' => '1021']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dtools.whazzup_vatsim_refresh'],
                ['key' => 'dbasic.whazzup_vatsim_refresh', 'group' => 'VATSIM', 'name' => 'Data Refresh Rate (secs)', 'field_type' => 'numeric', 'default' => '60', 'order' => '1022']
            );
        }
    }
}
