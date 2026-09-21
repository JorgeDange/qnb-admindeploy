<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('imobiliaria_id')->nullable()->constrained('imobiliarias')->nullOnDelete();
            $table->string('autor_nome', 255);
            $table->string('autor_email', 255);
            $table->unsignedTinyInteger('estrelas');
            $table->text('comentario')->nullable();
            $table->enum('estado', ['pendente', 'aprovada', 'rejeitada'])->default('pendente');
            $table->text('motivo_rejeicao')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('estrelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
