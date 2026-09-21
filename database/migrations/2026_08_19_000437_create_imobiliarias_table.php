<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('imobiliarias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nif')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('telefone');
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('password');
            $table->enum('estado', ['pendente', 'aprovada'])->default('pendente');
            $table->timestamp('aprovado_em')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

    }
};
