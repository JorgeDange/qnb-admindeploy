<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->text('motivo_suspensao')->nullable()->after('estado');
            $table->timestamp('suspensa_ate')->nullable()->after('motivo_suspensao');
            $table->foreignId('suspensa_por')->nullable()->after('suspensa_ate')->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('imobiliarias', function (Blueprint $table) {
            $table->dropForeign(['suspensa_por']);
            $table->dropColumn(['motivo_suspensao', 'suspensa_ate', 'suspensa_por']);
        });
    }
};
