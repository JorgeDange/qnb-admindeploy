<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo', 50);
            $table->string('titulo', 255);
            $table->text('mensagem');
            $table->string('url')->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'lida']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_notificacoes');
    }
};
