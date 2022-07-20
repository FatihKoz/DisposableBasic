<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDisposableSpecsTable extends Migration
{
    public function up()
    {
        // Add new fields for Specs usage
        if (Schema::hasTable('disposable_specs')) {
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->integer('selcal')->nullable()->after('crew');
                $table->integer('hexcode')->nullable()->after('selcal');
                $table->integer('rvr')->nullable()->after('hexcode');
                $table->integer('rmk')->nullable()->after('rvr');
            });
        }
    }
}
