<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pedidos_ativacao MODIFY estado ENUM('novo','em_negociacao','proposta_enviada','fechado','perdido') DEFAULT 'novo'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pedidos_ativacao MODIFY estado ENUM('novo','contactado','concluido') DEFAULT 'novo'");
        }
    }
};
