<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HandleDisposableRunwaysTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('turksim_runways')) {
            // Drop indexes for MariaDB compatibility
            Schema::table('turksim_runways', function (Blueprint $table) {
                $table->dropIndex(['id']);
                $table->dropUnique(['id']);
            });

            // Rename table
            Schema::rename('turksim_runways', 'disposable_runways');

            // Add indexes Back
            Schema::table('disposable_runways', function (Blueprint $table) {
                $table->index('id');
                $table->unique('id');
            });
        }

        if (!Schema::hasTable('disposable_runways')) {
            // Create Disposable Runways table
            Schema::create('disposable_runways', function (Blueprint $table) {
                $table->increments('id');
                $table->string('airport', 5);
                $table->string('runway_ident', 3);
                $table->string('lat', 12);
                $table->string('lon', 12);
                $table->string('heading', 3);
                $table->string('lenght', 5);
                $table->string('ils_freq', 7)->nullable();
                $table->string('loc_course', 3)->nullable();
                $table->string('airac', 4)->nullable();
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }

        if (Schema::hasTable('disposable_runways') && Schema::hasColumn('disposable_runways', 'airport')) {
            // Rename airport column
            Schema::table('disposable_runways', function (Blueprint $table) {
                $table->renameColumn('airport', 'airport_id');
            });
        }
    }
}
