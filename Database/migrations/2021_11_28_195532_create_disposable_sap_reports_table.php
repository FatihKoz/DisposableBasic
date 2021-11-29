<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposableSapReportsTable extends Migration
{
    public function up()
    {
        // Create Stable Approach Plugin (sap) Reports Table
        if (!Schema::hasTable('disposable_sap_reports')) {
            Schema::create('disposable_sap_reports', function (Blueprint $table) {
                $table->increments('id');
                $table->string('sap_analysisID', 100);
                $table->string('sap_userID', 100);
                $table->unsignedInteger('user_id');
                $table->string('pirep_id')->nullable();
                $table->mediumText('raw_report');
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }
    }
}
