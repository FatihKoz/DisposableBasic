<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HandleDisposableTechDetailsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('disposable_flaps') && !Schema::hasTable('disposable_tech')) {
            Schema::create('disposable_flaps', function (Blueprint $table) {
                $table->increments('id');
                $table->string('icao', 5);
                $table->string('f0_name', 10)->nullable()->default('UP');
                $table->string('f0_speed', 4)->nullable();
                $table->string('f1_name', 10)->nullable();
                $table->string('f1_speed', 4)->nullable();
                $table->string('f2_name', 10)->nullable();
                $table->string('f2_speed', 4)->nullable();
                $table->string('f3_name', 10)->nullable();
                $table->string('f3_speed', 4)->nullable();
                $table->string('f4_name', 10)->nullable();
                $table->string('f4_speed', 4)->nullable();
                $table->string('f5_name', 10)->nullable();
                $table->string('f5_speed', 4)->nullable();
                $table->string('f6_name', 10)->nullable();
                $table->string('f6_speed', 4)->nullable();
                $table->string('f7_name', 10)->nullable();
                $table->string('f7_speed', 4)->nullable();
                $table->string('f8_name', 10)->nullable();
                $table->string('f8_speed', 4)->nullable();
                $table->string('f9_name', 10)->nullable();
                $table->string('f9_speed', 4)->nullable();
                $table->string('f10_name', 10)->nullable();
                $table->string('f10_speed', 4)->nullable();
                $table->string('gear_extend', 4)->nullable();
                $table->string('gear_retract', 4)->nullable();
                $table->string('gear_maxtire', 4)->nullable();
                $table->boolean('active')->nullable()->default(true);
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
                $table->unique('icao');
            });
        }

        if (Schema::hasTable('disposable_flaps') && !Schema::hasTable('disposable_tech') && !Schema::hasColumn('disposable_flaps', 'max_pitch')) {
            Schema::table('disposable_flaps', function (Blueprint $table) {
                $table->integer('max_pitch')->nullable()->after('gear_maxtire');
                $table->integer('max_roll')->nullable()->after('max_pitch');
                $table->integer('max_cycle_a')->nullable()->after('max_roll');
                $table->integer('max_time_a')->nullable()->after('max_cycle_a');
                $table->integer('max_cycle_b')->nullable()->after('max_time_a');
                $table->integer('max_time_b')->nullable()->after('max_cycle_b');
                $table->integer('max_cycle_c')->nullable()->after('max_time_b');
                $table->integer('max_time_c')->nullable()->after('max_cycle_c');
            });
        }

        if (Schema::hasTable('disposable_flaps') && !Schema::hasTable('disposable_tech')) {
            Schema::table('disposable_flaps', function (Blueprint $table) {
                $table->dropIndex(['id']);
                $table->dropUnique(['id']);
                $table->dropUnique(['icao']);
            });

            Schema::rename('disposable_flaps', 'disposable_tech');

            Schema::table('disposable_tech', function (Blueprint $table) {
                $table->index('id');
                $table->unique('id');
                $table->unique('icao');
            });
        }

        if (Schema::hasTable('disposable_tech') && !Schema::hasColumn('disposable_tech', 'duration_a')) {
            Schema::table('disposable_tech', function (Blueprint $table) {
                $table->decimal('duration_a', $precision = 6, $scale = 2)->nullable()->after('max_time_a');
                $table->decimal('duration_b', $precision = 6, $scale = 2)->nullable()->after('max_time_b');
                $table->decimal('duration_c', $precision = 6, $scale = 2)->nullable()->after('max_time_c');
            });
        }

        if (Schema::hasTable('disposable_tech') && !Schema::hasColumn('disposable_tech', 'avg_fuel')) {
            Schema::table('disposable_tech', function (Blueprint $table) {
                $table->decimal('avg_fuel', $precision = 8, $scale = 2)->nullable()->after('duration_c');
            });
        }

        if (Schema::hasTable('disposable_tech') && !Schema::hasTable('disposable_tech_details')) {
            Schema::table('disposable_tech', function (Blueprint $table) {
                $table->dropIndex(['id']);
                $table->dropUnique(['id']);
                $table->dropUnique(['icao']);
            });

            Schema::rename('disposable_tech', 'disposable_tech_details');

            Schema::table('disposable_tech_details', function (Blueprint $table) {
                $table->index('id');
                $table->unique('id');
                $table->unique('icao');
            });
        }
    }
}
