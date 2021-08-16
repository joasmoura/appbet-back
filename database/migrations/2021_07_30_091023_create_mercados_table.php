<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regiao_id');
            $table->string('grupo');
            $table->string('dezena');
            $table->string('centena');
            $table->string('milhar');
            $table->string('duque_grupo');
            $table->string('terno_grupo');
            $table->string('terno_dezena');
            $table->string('milhar_centena');
            $table->string('milhar_invertida');
            $table->string('mc_invertida');
            $table->string('centena_invertida');
            $table->string('duque_dezena');
            $table->string('passe_combinado');
            $table->string('terno_grupo_combinado');
            $table->string('passe_seco');
            $table->string('grupo_combinado');
            $table->string('terno_dezena_cercado');
            $table->string('queima');
            $table->timestamps();

            $table->foreign('regiao_id')
            ->references('id')
            ->on('regioes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mercados');
    }
}
