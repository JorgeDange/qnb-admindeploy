<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->enum('estado', ['pendente', 'aprovada', 'suspensa'])->default('pendente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->enum('estado', ['pendente', 'aprovada'])->default('pendente')->change();
        });
    }
};
