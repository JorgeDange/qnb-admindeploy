<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscricao_id')->constrained('imobiliaria_plano')->cascadeOnDelete();
            $table->foreignId('imobiliaria_id')->constrained('imobiliarias')->cascadeOnDelete();
            $table->decimal('valor', 12, 2);
            $table->enum('moeda', ['Kz', 'USD'])->default('Kz');
            $table->enum('metodo', ['transferencia', 'multicaixa', 'dinheiro', 'outro']);
            $table->string('referencia')->nullable();
            $table->string('comprovativo', 500)->nullable();
            $table->enum('estado', ['pendente', 'confirmado', 'rejeitado', 'reembolsado'])->default('pendente');
            $table->foreignId('confirmado_por')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('confirmado_em')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('imobiliaria_id');
        });

        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->foreignId('pagamento_id')->constrained('pagamentos')->cascadeOnDelete();
            $table->foreignId('imobiliaria_id')->constrained('imobiliarias')->cascadeOnDelete();
            $table->decimal('valor', 12, 2);
            $table->enum('moeda', ['Kz', 'USD'])->default('Kz');
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('emitida_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faturas');
        Schema::dropIfExists('pagamentos');
    }
};
