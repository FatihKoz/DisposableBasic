<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDisposableTechDetailsTable extends Migration
{
    public function up()
    {
        // Add weight fields for VA's not forcing SimBrief and Specs usage
        if (Schema::hasTable('disposable_tech_details')) {
            Schema::table('disposable_tech_details', function (Blueprint $table) {
                $table->integer('mzfw')->nullable()->after('avg_fuel');
                $table->integer('mrw')->nullable()->after('mzfw');
                $table->integer('mtow')->nullable()->after('mrw');
                $table->integer('mlaw')->nullable()->after('mtow');
            });
        }
    }
}
