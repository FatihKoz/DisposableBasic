<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposableRandomFlightTable extends Migration
{
    public function up()
    {
        // Create Disposable RandomFlight Table
        if (!Schema::hasTable('disposable_randomflight')) {
            Schema::create('disposable_randomflight', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('airport_id', 5)->nullable();
                $table->string('flight_id', 100)->nullable();
                $table->date('assign_date')->nullable();
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }
    }
}
