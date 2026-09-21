<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios_agendados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tipo', 100);
            $table->enum('frequencia', ['diario', 'semanal', 'mensal']);
            $table->enum('formato', ['csv', 'pdf', 'xlsx'])->default('pdf');
            $table->json('destinatarios');
            $table->json('filtros')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultima_execucao')->nullable();
            $table->timestamp('proxima_execucao')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios_agendados');
    }
};
