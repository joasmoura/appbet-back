<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComissoesGerentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissoes_gerentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aposta_id')->nullable();
            $table->double('valor',10,2);
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
        Schema::dropIfExists('comissoes_gerentes');
    }
}
