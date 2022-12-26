<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDisposableWhazzupChecksTable extends Migration
{
    public function up()
    {
        // Update WhazzUp Checks Table
        if (Schema::hasTable('disposable_whazzup_checks')) {
            Schema::table('disposable_whazzup_checks', function (Blueprint $table) {
                $table->string('callsign', 20)->nullable()->after('is_online');
            });
        }

        // Add New Settings for WhazzUp Checks and Auto Reject
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_callsign'],
                ['group' => 'Network Checks', 'name' => 'Check Network Callsign', 'field_type' => 'check', 'default' => 'false', 'order' => '8808']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.autoreject'],
                ['group' => 'Auto Reject', 'name' => 'Auto Reject Pireps', 'field_type' => 'check', 'default' => 'false', 'order' => '8901']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_presence'],
                ['group' => 'Auto Reject', 'name' => 'Network Presence', 'field_type' => 'check', 'default' => 'false', 'order' => '8902']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_callsign'],
                ['group' => 'Auto Reject', 'name' => 'Network Callsign', 'field_type' => 'check', 'default' => 'false', 'order' => '8903']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginscore'],
                ['group' => 'Auto Reject', 'name' => 'Pirep Score', 'field_type' => 'numeric', 'default' => '0', 'order' => '8904']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginlrate'],
                ['group' => 'Auto Reject', 'name' => 'Landing Rate', 'field_type' => 'numeric', 'default' => '0', 'order' => '8905']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginftime'],
                ['group' => 'Auto Reject', 'name' => 'Flight Time', 'field_type' => 'numeric', 'default' => '0', 'order' => '8906']
            );
        }
    }
}
