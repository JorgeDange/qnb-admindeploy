<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('imovel_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('caminho');
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('capa')->default(false);
            $table->timestamps();
        });

    }
};
