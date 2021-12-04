<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HandleDisposableRandomFlightsTable extends Migration
{
    public function up()
    {
        // Create Disposable Random Flight table
        if (!Schema::hasTable('disposable_randomflight') && !Schema::hasTable('disposable_random_flights')) {
            Schema::create('disposable_randomflight', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('airport_id', 5)->nullable();
                $table->string('flight_id', 150)->nullable();
                $table->string('pirep_id', 150)->nullable();
                $table->date('assign_date')->nullable();
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }

        // Update Disposable Random Flight table
        if (Schema::hasTable('disposable_randomflight') && !Schema::hasTable('disposable_random_flights') && !Schema::hasColumn('disposable_randomflight', 'pirep_id')) {
            Schema::table('disposable_randomflight', function (Blueprint $table) {
                $table->string('flight_id', 150)->nullable()->change();
                $table->string('pirep_id', 150)->nullable()->after('flight_id');
            });
        }

        // Rename to Disposable Random Flights
        if (Schema::hasTable('disposable_randomflight') && !Schema::hasTable('disposable_random_flights')) {
            Schema::table('disposable_randomflight', function (Blueprint $table) {
                $table->dropIndex(['id']);
                $table->dropUnique(['id']);
            });

            Schema::rename('disposable_randomflight', 'disposable_random_flights');

            Schema::table('disposable_random_flights', function (Blueprint $table) {
                $table->index('id');
                $table->unique('id');
            });
        }
    }
}
