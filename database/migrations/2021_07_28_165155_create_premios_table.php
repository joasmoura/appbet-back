<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premios_horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horarios_id');
            $table->string('premio_1')->nullable();
            $table->string('premio_2')->nullable();
            $table->string('premio_3')->nullable();
            $table->string('premio_4')->nullable();
            $table->string('premio_5')->nullable();
            $table->string('premio_6')->nullable();
            $table->string('premio_7')->nullable();
            $table->timestamps();

            $table->foreign('horarios_id')
            ->references('id')
            ->on('horarios_extracao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('premios_horarios');
    }
}
