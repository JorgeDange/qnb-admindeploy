<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('depoimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cargo')->nullable();
            $table->text('texto');
            $table->unsignedTinyInteger('estrelas')->default(5);
            $table->string('avatar')->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

    }
};
