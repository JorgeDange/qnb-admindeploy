<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            // Estado e tipo
            $table->string('estado', 30)->default('pendente')->after('moeda');
            $table->string('tipo', 30)->default('proforma')->after('estado');

            // Comprovativo
            $table->string('comprovativo_path', 500)->nullable()->after('pdf_path');
            $table->timestamp('comprovativo_enviado_em')->nullable()->after('comprovativo_path');
            $table->unsignedBigInteger('comprovativo_enviado_por')->nullable()->after('comprovativo_enviado_em');

            // Aprovação / Rejeição
            $table->unsignedBigInteger('aprovada_por')->nullable()->after('comprovativo_enviado_por');
            $table->timestamp('aprovada_em')->nullable()->after('aprovada_por');
            $table->text('motivo_rejeicao')->nullable()->after('aprovada_em');
            $table->unsignedBigInteger('rejeitada_por')->nullable()->after('motivo_rejeicao');
            $table->timestamp('rejeitada_em')->nullable()->after('rejeitada_por');

            // Recibo
            $table->string('recibo_numero', 50)->nullable()->after('rejeitada_em');
            $table->string('recibo_pdf_path', 500)->nullable()->after('recibo_numero');
            $table->timestamp('recibo_emitido_em')->nullable()->after('recibo_pdf_path');

            // Email timestamps
            $table->timestamp('email_emitida_em')->nullable()->after('recibo_emitido_em');
            $table->timestamp('email_comprovativo_em')->nullable()->after('email_emitida_em');
            $table->timestamp('email_paga_em')->nullable()->after('email_comprovativo_em');
        });
    }

    public function down(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            $table->dropColumn([
                'estado', 'tipo',
                'comprovativo_path', 'comprovativo_enviado_em', 'comprovativo_enviado_por',
                'aprovada_por', 'aprovada_em', 'motivo_rejeicao', 'rejeitada_por', 'rejeitada_em',
                'recibo_numero', 'recibo_pdf_path', 'recibo_emitido_em',
                'email_emitida_em', 'email_comprovativo_em', 'email_paga_em',
            ]);
        });
    }
};
