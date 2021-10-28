<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HandleDisposableSpecsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('turksim_specs')) {
            // Drop indexes for MariaDB compatibility
            Schema::table('turksim_specs', function (Blueprint $table) {
                $table->dropIndex('turksim_specs_id_index');
                $table->dropUnique('turksim_specs_id_unique');
            });

            // Rename table
            Schema::rename('turksim_specs', 'disposable_specs');

            // Add indexes back
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->index('id');
                $table->unique('id');
            });
        }

        if (Schema::hasTable('disposable_specs') && !Schema::hasColumn('disposable_specs', 'airframe_id')) {
            // Add SimBrief Airframe ID to Disposable Specs table
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->string('airframe_id', 50)->nullable()->after('subfleet_id');
            });
        }

        if (Schema::hasTable('disposable_specs') && !Schema::hasColumn('disposable_specs', 'icao')) {
            // Add Aircraft ICAO, Name, Engines and Cruise Level Offset columns
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->string('icao', 5)->nullable()->after('stitle');
                $table->string('name', 20)->nullable()->after('icao');
                $table->string('engines', 20)->nullable()->after('name');
                $table->string('cruiselevel', 6)->nullable()->after('fuelfactor');
            });
        }

        if (Schema::hasTable('disposable_specs') && !Schema::hasColumn('disposable_specs', 'paxwgt')) {
            // Add Passenger and Baggage Weight columns
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->string('paxwgt', 3)->nullable()->after('cruiselevel');
                $table->string('bagwgt', 3)->nullable()->after('paxwgt');
            });
        }

        if (Schema::hasTable('disposable_specs') && !Schema::hasColumn('disposable_specs', 'icao_id')) {
            // Add main icao type code identifier
            Schema::table('disposable_specs', function (Blueprint $table) {
                $table->string('icao_id', 5)->nullable()->after('id');
            });
        }

        if (!Schema::hasTable('disposable_specs')) {
            // Create Disposable Specs table
            Schema::create('disposable_specs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('icao_id', 5)->nullable();
                $table->unsignedInteger('aircraft_id')->nullable();
                $table->unsignedInteger('subfleet_id')->nullable();
                $table->string('airframe_id', 50)->nullable();
                $table->string('saircraft', 50);
                $table->string('stitle', 30)->nullable();
                $table->string('icao', 5)->nullable();
                $table->string('name', 20)->nullable();
                $table->string('engines', 20)->nullable();
                $table->string('bew', 6)->nullable();
                $table->string('dow', 6)->nullable();
                $table->string('mzfw', 6)->nullable();
                $table->string('mrw', 6)->nullable();
                $table->string('mtow', 6)->nullable();
                $table->string('mlw', 6)->nullable();
                $table->string('mrange', 5)->nullable();
                $table->string('mceiling', 5)->nullable();
                $table->string('mfuel', 6)->nullable();
                $table->string('fuelfactor', 3)->nullable();
                $table->string('cruiselevel', 6)->nullable();
                $table->string('paxwgt', 3)->nullable();
                $table->string('bagwgt', 3)->nullable();
                $table->string('mpax', 6)->nullable();
                $table->string('mspeed', 4)->nullable();
                $table->string('cspeed', 4)->nullable();
                $table->string('cat', 1)->nullable();
                $table->string('equip', 30)->nullable();
                $table->string('transponder', 10)->nullable();
                $table->string('pbn', 30)->nullable();
                $table->string('crew', 2)->nullable();
                $table->boolean('active')->nullable()->default(false);
                $table->timestamps();
                $table->index('id');
                $table->unique('id');
            });
        }
    }
}
