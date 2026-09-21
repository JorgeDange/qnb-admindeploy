<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imobiliaria_plano', function (Blueprint $table) {
            $table->dropForeign(['imobiliaria_id']);
            $table->dropUnique(['imobiliaria_id']);
            $table->foreign('imobiliaria_id')->references('id')->on('imobiliarias')->cascadeOnDelete();

            $table->string('estado', 20)->default('ativa')->after('ativo');
            $table->boolean('renovacao_automatica')->default(false)->after('estado');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('imobiliaria_plano', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['estado', 'renovacao_automatica']);
            $table->dropForeign(['imobiliaria_id']);
            $table->dropUnique(['imobiliaria_id']);
            $table->foreign('imobiliaria_id')->references('id')->on('imobiliarias')->cascadeOnDelete();
        });
    }
};
