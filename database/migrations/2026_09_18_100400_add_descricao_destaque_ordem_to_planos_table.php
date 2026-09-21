<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('nome');
            $table->boolean('destaque')->default(false)->after('ativo');
            $table->integer('ordem')->default(0)->after('destaque');
        });
    }

    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'destaque', 'ordem']);
        });
    }
};
