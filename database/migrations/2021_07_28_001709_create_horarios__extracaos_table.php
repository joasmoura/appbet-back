<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosExtracaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios_extracao', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('hora');
            $table->unsignedBigInteger('extracao_id');
            $table->unsignedBigInteger('regiao_id')->nullable();
            $table->timestamps();

            $table->foreign('regiao_id')
            ->references('id')
            ->on('regioes');

            $table->foreign('extracao_id')
            ->references('id')
            ->on('extracoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios_extracao');
    }
}
