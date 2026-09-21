<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->string('estado_imovel')->nullable()->after('descricao');
            $table->unsignedSmallInteger('estacionamento')->nullable()->after('ano_construcao');
        });
    }
};
