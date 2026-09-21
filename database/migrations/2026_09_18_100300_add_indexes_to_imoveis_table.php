<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->index('estado');
            $table->index('provincia');
            $table->index('imobiliaria_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['provincia']);
            $table->dropIndex(['imobiliaria_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
