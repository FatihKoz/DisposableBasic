<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        // Add General Settings for Widgets
        if (Schema::hasTable('disposable_settings')) {
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.actransfer_discount'],
                ['group' => 'General', 'name' => 'A.Transfer Discount Percent', 'field_type' => 'numeric', 'default' => '0', 'order' => '7001']
            );
            DB::table('disposable_settings')->updateOrInsert(
                ['key' => 'dbasic.jumpseat_discount'],
                ['group' => 'General', 'name' => 'JumpSeat Discount Percent', 'field_type' => 'numeric', 'default' => '0', 'order' => '7002']
            );
        }
    }
};
