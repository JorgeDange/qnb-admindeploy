<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('tipo', ['verificacao_email', 'recuperacao_password']);
            $table->timestamp('expira_em');
            $table->timestamp('usado_em')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_tokens');
    }
};
