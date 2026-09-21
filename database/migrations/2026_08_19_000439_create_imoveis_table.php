<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->constrained()->cascadeOnDelete();
            $table->string('referencia')->unique();
            $table->string('titulo');
            $table->enum('tipo', ['apartamento', 'vivenda', 'terreno', 'loja', 'escritorio', 'armazem', 'quintal']);
            $table->enum('finalidade', ['arrendar', 'comprar', 'vender']);
            $table->decimal('preco', 14, 2);
            $table->enum('moeda', ['Kz', 'USD'])->default('Kz');
            $table->decimal('area', 10, 2)->nullable();
            $table->unsignedSmallInteger('quartos')->nullable();
            $table->unsignedSmallInteger('wc')->nullable();
            $table->unsignedSmallInteger('ano_construcao')->nullable();
            $table->string('video')->nullable();
            $table->string('provincia');
            $table->string('municipio');
            $table->string('bairro')->nullable();
            $table->string('endereco')->nullable();
            $table->text('descricao');
            $table->enum('estado', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->enum('disponibilidade', ['disponivel', 'vendido', 'arrendado'])->default('disponivel');
            $table->boolean('destaque')->default(false);
            $table->unsignedInteger('visualizacoes')->default(0);
            $table->unsignedInteger('contactos')->default(0);
            $table->timestamps();

            $table->index(['estado', 'disponibilidade']);
            $table->index(['provincia', 'municipio']);
        });

    }
};
