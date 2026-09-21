<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0)->after('total');
            $table->decimal('desconto', 12, 2)->default(0)->after('subtotal');
            $table->decimal('retencao', 12, 2)->default(0)->after('desconto');
            $table->text('nota')->nullable()->after('retencao');

            // Snapshot da empresa no momento da emissão
            $table->string('empresa_nome', 200)->nullable()->after('nota');
            $table->string('empresa_nif', 20)->nullable()->after('empresa_nome');
            $table->string('empresa_endereco', 500)->nullable()->after('empresa_nif');
            $table->string('empresa_telefone', 30)->nullable()->after('empresa_endereco');
            $table->string('empresa_email', 150)->nullable()->after('empresa_telefone');
            $table->string('banco_nome', 100)->nullable()->after('empresa_email');
            $table->string('banco_iban', 60)->nullable()->after('banco_nome');
            $table->string('banco_titular', 200)->nullable()->after('banco_iban');
        });
    }

    public function down(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 'desconto', 'retencao', 'nota',
                'empresa_nome', 'empresa_nif', 'empresa_endereco',
                'empresa_telefone', 'empresa_email',
                'banco_nome', 'banco_iban', 'banco_titular',
            ]);
        });
    }
};
