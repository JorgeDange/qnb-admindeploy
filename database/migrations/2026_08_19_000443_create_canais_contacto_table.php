<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('canais_contacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->nullable()->constrained('imobiliarias')->cascadeOnDelete();
            $table->foreignId('imovel_id')->nullable()->constrained('imoveis')->cascadeOnDelete();
            $table->enum('tipo', ['whatsapp', 'telefone', 'email', 'facebook', 'instagram', 'linkedin']);
            $table->string('valor');
            $table->timestamps();
        });

    }
};
