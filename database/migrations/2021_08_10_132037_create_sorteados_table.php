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
            $table->string('numero_premio');
            $table->string('numero_sorteado');
            $table->double('valor',10,2);
            $table->timestamps();

            $table->foreign('resultado_id')
            ->references('id')
            ->on('premios_horarios')
            ->onDelete('cascade');

            $table->foreign('item_aposta_id')
            ->references('id')
            ->on('itens_apostas')
            ->onDelete('cascade');
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
