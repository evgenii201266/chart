<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('object_id')->unique();
            $table->float('pamp_temp', 8, 3);
            $table->float('pamp_voltage', 8, 3);
            $table->float('system_voltage', 8, 3)->nullable();
            $table->float('battery_voltage', 8, 3)->nullable();
            $table->timestamp('date')->nullable();
            $table->json('options')->nullable();	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test');
    }
}
