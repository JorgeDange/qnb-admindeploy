<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('imobiliaria_plano', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imobiliaria_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('plano_id')->constrained();
            $table->unsignedInteger('posts_usados')->default(0);
            $table->date('data_inicio')->nullable();
            $table->date('data_expiracao')->nullable();
            $table->boolean('ativo')->default(false);
            $table->timestamps();
        });

    }
};
