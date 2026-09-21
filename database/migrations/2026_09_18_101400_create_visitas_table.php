<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('imobiliaria_id')->nullable()->constrained('imobiliarias')->nullOnDelete();
            $table->string('cliente_nome', 255);
            $table->string('cliente_email', 255);
            $table->string('cliente_telefone', 50);
            $table->dateTime('data_visita');
            $table->enum('estado', ['pendente', 'confirmada', 'concluida', 'cancelada'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('data_visita');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
