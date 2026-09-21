<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('tipo', 20)->default('string')->after('valor');
            $table->string('grupo', 50)->default('geral')->after('tipo');
            $table->string('descricao', 255)->nullable()->after('grupo');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'grupo', 'descricao']);
        });
    }
};
