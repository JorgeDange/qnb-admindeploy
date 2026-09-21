<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imobiliaria_notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->constrained('imobiliarias')->cascadeOnDelete();
            $table->string('tipo', 100);
            $table->string('titulo');
            $table->text('mensagem')->nullable();
            $table->string('url', 500)->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['imobiliaria_id', 'lida']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imobiliaria_notificacoes');
    }
};
