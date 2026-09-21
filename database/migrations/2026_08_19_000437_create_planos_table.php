<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedInteger('posts_limite');
            $table->unsignedInteger('dias_validade');
            $table->decimal('preco', 12, 2);
            $table->enum('moeda', ['Kz', 'USD'])->default('Kz');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

    }
};
