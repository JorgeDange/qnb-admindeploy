<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone', 50)->nullable();
            $table->string('password');
            $table->timestamp('email_verificado_em')->nullable();
            $table->enum('estado', ['ativo', 'suspenso', 'bloqueado'])->default('ativo');
            $table->timestamp('aceitou_termos_em')->nullable();
            $table->string('aceitou_termos_ip', 45)->nullable();
            $table->timestamp('ultimo_login')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
