<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('disposable_sceneries')) {
            Schema::create('disposable_sceneries', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->string('airport_id', 5);
                $table->unsignedSmallInteger('region')->nullable();
                $table->unsignedSmallInteger('simulator')->nullable();
                $table->mediumText('notes')->nullable();
                $table->timestamps();
            });
        }
    }
};
