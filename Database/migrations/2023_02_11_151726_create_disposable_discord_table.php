<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposableDiscordTable extends Migration
{
    public function up()
    {
        // Create Disposable Discord Widget Table
        if (!Schema::hasTable('disposable_discord')) {
            Schema::create('disposable_discord', function (Blueprint $table) {
                $table->increments('id');
                $table->string('server_id', 100)->nullable();
                $table->mediumText('rawdata')->nullable();
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }
    }
}
