<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE mensagens MODIFY COLUMN origem ENUM('imovel', 'institucional', 'plano', 'cliente') NOT NULL DEFAULT 'imovel'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE mensagens MODIFY COLUMN origem ENUM('imovel', 'institucional', 'plano') NOT NULL DEFAULT 'imovel'");
        }
    }
};
