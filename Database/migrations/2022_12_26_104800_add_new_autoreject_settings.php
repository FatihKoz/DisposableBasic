<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        // Add New Settings for Auto Reject
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_marginthrdist'],
                ['group' => 'Auto Reject', 'name' => 'Threshold Distance', 'field_type' => 'numeric', 'default' => '0', 'order' => '8907']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.ar_margingforce'],
                ['group' => 'Auto Reject', 'name' => 'G-Force', 'field_type' => 'numeric', 'default' => '0', 'order' => '8908']
            );
        }
    }
};
