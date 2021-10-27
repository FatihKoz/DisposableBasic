<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

class RenameDisposableRandomFlightTable extends Migration
{
    public function up()
    {
        // Rename Disposable RandomFlight Table
        if (Schema::hasTable('disposable_randomflight')) {
            Schema::rename('disposable_randomflight', 'disposable_random_flights');
        }
    }
}
