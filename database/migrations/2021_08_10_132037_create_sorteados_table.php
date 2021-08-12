<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSorteadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorteados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resultado_id');
            $table->unsignedBigInteger('item_aposta_id');
            $table->integer('numero_premio');
            $table->integer('numero_sorteado');
            $table->double('valor',10,2);
            $table->timestamps();

            $table->foreign('resultado_id')
            ->references('id')
            ->on('premios_horarios');

            $table->foreign('item_aposta_id')
            ->references('id')
            ->on('itens_apostas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorteados');
    }
}
