<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDisposableRandomFlightTable extends Migration
{
  public function up()
  {
    if (Schema::hasTable('disposable_randomflight')) {
      // Update Disposable RandomFlight Table
      Schema::table('disposable_randomflight', function (Blueprint $table) {
        $table->string('flight_id', 150)->nullable()->change();
        $table->string('pirep_id', 150)->nullable()->after('flight_id');
      });
    }
  }
}
