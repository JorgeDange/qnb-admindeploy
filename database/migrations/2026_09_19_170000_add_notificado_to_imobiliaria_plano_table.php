<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imobiliaria_plano', function (Blueprint $table) {
            $table->boolean('notificado_a_expirar')->default(false)->after('renovacao_automatica');
            $table->boolean('notificado_expirado')->default(false)->after('notificado_a_expirar');
        });
    }

    public function down(): void
    {
        Schema::table('imobiliaria_plano', function (Blueprint $table) {
            $table->dropColumn(['notificado_a_expirar', 'notificado_expirado']);
        });
    }
};
