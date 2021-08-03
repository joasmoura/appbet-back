<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItensApostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itens_apostas', function (Blueprint $table) {
            $table->id();
            $table->integer('modalidade');
            $table->unsignedBigInteger('aposta_id');
            $table->double('valor',10,2);
            $table->double('subtotal',10,2);
            $table->json('numero');
            $table->double('poss_ganho',10,2);
            $table->integer('premio_de');
            $table->integer('premio_ate');
            $table->timestamps();


            $table->foreign('aposta_id')
            ->references('id')
            ->on('apostas')
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
        Schema::dropIfExists('itens_apostas');
    }
}
