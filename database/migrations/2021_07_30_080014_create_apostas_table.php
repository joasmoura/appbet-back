<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apostas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horario_id');
            $table->integer('codigo')->unique();
            $table->unsignedBigInteger('user_id');
            $table->double('total',10,2);
            $table->enum('status',['aberto','ganhou','perdeu','cancelado'])->default('aberto');
            $table->string('tel_apostador')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users');

            $table->foreign('horario_id')
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
        Schema::dropIfExists('apostas');
    }
}
