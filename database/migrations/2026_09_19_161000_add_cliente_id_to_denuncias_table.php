<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->foreignId('cliente_id')
                ->nullable()
                ->after('imobiliaria_id')
                ->constrained('clientes')
                ->nullOnDelete();

            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropIndex(['cliente_id']);
            $table->dropColumn('cliente_id');
        });
    }
};
