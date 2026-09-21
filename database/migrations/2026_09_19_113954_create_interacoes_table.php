<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('imovel_id')->nullable()->constrained('imoveis')->nullOnDelete();
            $table->foreignId('imobiliaria_id')->nullable()->constrained('imobiliarias')->nullOnDelete();
            $table->enum('tipo', [
                'visualizacao',
                'contacto_visto',
                'mensagem_enviada',
                'visita_agendada',
                'favorito_adicionado',
                'denuncia',
            ]);
            $table->json('metadados')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('cliente_id');
            $table->index('imovel_id');
            $table->index('imobiliaria_id');
            $table->index('tipo');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interacoes');
    }
};
