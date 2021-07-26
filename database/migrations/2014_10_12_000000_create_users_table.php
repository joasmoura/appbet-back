<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('comissao_faturamento',10,2)->nullable();
            $table->double('comissao_lucro',10,2)->nullable();
            $table->double('limite_credito',10,2)->nullable();
            $table->double('percentual_premio',10,2)->nullable();
            $table->string('telefone',)->nullable();

            $table->unsignedBigInteger('regiao_id')->nullable();
            $table->unsignedBigInteger('gerente_id')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('comissao_id')->nullable();
            $table->enum('perfil',['supervisor','gerente','administrador','cambista']);
            $table->boolean('status')->default('1');//0 - Desativado | 1 - Ativo

            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('regiao_id')
            ->references('id')
            ->on('regioes');

            $table->foreign('gerente_id')
            ->references('id')
            ->on('users');

            $table->foreign('supervisor_id')
            ->references('id')
            ->on('users');

            $table->foreign('comissao_id')
            ->references('id')
            ->on('comissoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
