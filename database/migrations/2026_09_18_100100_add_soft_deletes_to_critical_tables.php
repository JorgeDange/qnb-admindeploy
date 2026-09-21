<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('planos', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('imoveis', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('planos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
