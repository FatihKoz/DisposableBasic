<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        // Rename length column
        if (Schema::hasTable('disposable_runways') && Schema::hasColumn('disposable_runways', 'lenght')) {
            Schema::table('disposable_runways', function (Blueprint $table) {
                $table->renameColumn('lenght', 'length');
            });
        }
    }
};
