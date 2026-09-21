<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->timestamp('ultimo_login')->nullable()->after('ativo');
            $table->string('ultimo_ip', 45)->nullable()->after('ultimo_login');
            $table->integer('tentativas_login')->default(0)->after('ultimo_ip');
            $table->timestamp('bloqueado_ate')->nullable()->after('tentativas_login');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['ultimo_login', 'ultimo_ip', 'tentativas_login', 'bloqueado_ate']);
        });
    }
};
