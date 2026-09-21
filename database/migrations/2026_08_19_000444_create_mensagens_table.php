<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->constrained('imobiliarias')->cascadeOnDelete();
            $table->foreignId('imovel_id')->nullable()->constrained('imoveis')->nullOnDelete();
            $table->string('nome');
            $table->string('contacto');
            $table->text('texto');
            $table->enum('origem', ['imovel', 'institucional', 'plano'])->default('imovel');
            $table->boolean('lida')->default(false);
            $table->timestamps();
        });

    }
};
