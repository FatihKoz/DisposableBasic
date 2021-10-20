<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposableSettingsTable extends Migration
{
  public function up()
  {
    if (!Schema::hasTable('disposable_settings')) {
      // Create Disposable Settings Table
      Schema::create('disposable_settings', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name', 200)->nullable();
        $table->string('key', 100);
        $table->string('value', 500)->nullable();
        $table->string('group', 100)->nullable();
        $table->timestamps();
        $table->index('id');
        $table->unique('id');
        $table->unique('key');
      });
    }
  }
}
