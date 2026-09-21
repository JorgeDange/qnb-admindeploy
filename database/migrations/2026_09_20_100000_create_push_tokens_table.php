<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->string('token', 500);
            $table->string('plataforma', 20)->default('web');
            $table->string('user_agent', 500)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_uso_em')->nullable();
            $table->timestamps();

            $table->unique(['cliente_id', 'token']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
    }
};
