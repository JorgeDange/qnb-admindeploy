<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('imobiliaria_id')->nullable()->constrained('imobiliarias')->nullOnDelete();
            $table->string('autor_nome', 255);
            $table->string('autor_email', 255);
            $table->string('autor_telefone', 50)->nullable();
            $table->enum('motivo', ['spam', 'informacao_falsa', 'imovel_inexistente', 'preco_incorreto', 'violacao', 'outro']);
            $table->text('descricao');
            $table->enum('estado', ['pendente', 'em_analise', 'resolvida', 'arquivada'])->default('pendente');
            $table->text('resolucao')->nullable();
            $table->foreignId('resolvido_por')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('motivo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
