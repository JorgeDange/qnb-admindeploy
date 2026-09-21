<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('imovel_amenidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->foreignId('amenidade_id')->constrained('amenidades')->cascadeOnDelete();
            $table->unique(['imovel_id', 'amenidade_id']);
        });

    }
};
