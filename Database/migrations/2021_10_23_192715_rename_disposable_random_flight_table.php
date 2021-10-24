<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

class RenameDisposableRandomFlightTable extends Migration
{
  public function up()
  {
    if (Schema::hasTable('disposable_randomflight')) {
      // Rename Disposable RandomFlight Table
      Schema::rename('disposable_randomflight', 'disposable_random_flights');
    }
  }
}
