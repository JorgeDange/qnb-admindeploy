<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('pedidos_ativacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->constrained()->cascadeOnDelete();
            $table->string('plano_pretendido')->nullable();
            $table->text('mensagem')->nullable();
            $table->enum('estado', ['novo', 'contactado', 'concluido'])->default('novo');
            $table->timestamps();
        });

    }
};
