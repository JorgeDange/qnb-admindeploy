<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->nullable()->constrained('admin_mensagens')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('imobiliaria_id')->constrained('imobiliarias')->cascadeOnDelete();
            $table->enum('autor_tipo', ['admin', 'imobiliaria']);
            $table->string('assunto')->nullable();
            $table->text('texto');
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('imobiliaria_id');
            $table->index('thread_id');
            $table->index('lida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_mensagens');
    }
};
