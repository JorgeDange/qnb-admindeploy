<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interacoes', function (Blueprint $table) {
            $table->enum('tipo', [
                'visualizacao',
                'contacto_visto',
                'mensagem_enviada',
                'visita_agendada',
                'favorito_adicionado',
                'denuncia',
                'avaliacao',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('interacoes', function (Blueprint $table) {
            $table->enum('tipo', [
                'visualizacao',
                'contacto_visto',
                'mensagem_enviada',
                'visita_agendada',
                'favorito_adicionado',
                'denuncia',
            ])->change();
        });
    }
};
