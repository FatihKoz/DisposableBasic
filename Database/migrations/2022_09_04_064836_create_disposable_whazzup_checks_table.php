<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposableWhazzupChecksTable extends Migration
{
    public function up()
    {
        // Create WhazzUp Checks Table
        if (!Schema::hasTable('disposable_whazzup_checks')) {
            Schema::create('disposable_whazzup_checks', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->string('pirep_id')->nullable();
                $table->string('network', 10);
                $table->boolean('is_online')->nullable()->default(false);
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }

        // Add Settings related to WhazzUp checks
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck'],
                ['group' => 'Network Checks', 'name' => 'Check Network Presence', 'field_type' => 'check', 'default' => 'false', 'order' => '8801']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_server'], 
                ['group' => 'Network Checks', 'name' => 'Server Selection', 'field_type' => 'select', 'options' => 'IVAO,VATSIM', 'default' => 'IVAO', 'order' => '8802']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_fieldname'],
                ['group' => 'Network Checks', 'name' => 'User Field Name', 'default' => 'IVAO ID', 'order' => '8803']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_margin'],
                ['group' => 'Network Checks', 'name' => 'Validity Percentage', 'field_type' => 'numeric', 'default' => '75', 'order' => '8804']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.networkcheck_cleanup'],
                ['group' => 'Network Checks', 'name' => 'Hours to Cleanup Check Data', 'field_type' => 'numeric', 'default' => '48', 'order' => '8805']
            );
        }
    }
}
