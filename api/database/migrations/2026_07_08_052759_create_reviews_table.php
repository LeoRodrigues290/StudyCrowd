<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==============================================================================
// MIGRATION: CRIAR TABELA DE AVALIAÇÕES (reviews)
// ==============================================================================
//
// AULA DE ARQUITETURA SÊNIOR — REGRAS DE NEGÓCIO E RESTRIÇÃO DE UNICIDADE:
// -------------------------------------------------------------------------
// 1. O Requisito do Briefing:
//    "Sistema de avaliações por categoria: nota de limpeza, localização e 
//     custo-benefício separadas, com média calculada."
//    Usamos unsignedTinyInteger para guardar notas inteiras de 1 a 5.
//    E guardamos a média geral (overall_score) em decimal(3, 2) — ex: 4.67 — 
//    para consultas e ranqueamento ultra-rápidos sem precisar calcular na hora!
//
// 2. Proteção contra Avaliações Duplicadas (Unique Constraint):
//    Um estudante não deveria poder avaliar o MESMO imóvel 10 vezes para manipular
//    a nota dele (para cima ou para baixo).
//    A instrução ->unique(['property_id', 'user_id']) diz ao MySQL: "Esta combinação
//    EXATA de imóvel e usuário só pode existir 1 única vez em toda a tabela".
//    Se tentarem inserir a segunda, o banco bloqueia!
// ==============================================================================

return new class extends Migration
{
    /**
     * Executa a migration (Cria a estrutura da tabela no MySQL).
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            // 1. Chave Primária
            $table->id();

            // 2. Relacionamentos (Chaves Estrangeiras)
            $table->foreignId('property_id')
                  ->constrained('properties')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // 3. Notas por Categoria (1 a 5)
            // unsignedTinyInteger ocupa apenas 1 byte (suporta de 0 a 255).
            $table->unsignedTinyInteger('cleanliness_score'); // Limpeza
            $table->unsignedTinyInteger('location_score');    // Localização
            $table->unsignedTinyInteger('value_score');       // Custo-benefício

            // 4. Média Geral Calculada
            // decimal(3, 2) permite números como 0.00 até 9.99 (ex: 4.67).
            // Colocamos index() porque na Fase 2 filtraremos imóveis pelas notas mais altas!
            $table->decimal('overall_score', 3, 2)->index();

            // 5. Comentário em Texto (Opcional)
            // nullable() permite que o estudante dê apenas a nota em estrelas sem escrever texto.
            $table->text('comment')->nullable();

            // 6. Timestamps do Laravel
            $table->timestamps();

            // ==========================================================================
            // RESTRIÇÃO DE UNICIDADE (Um usuário = Uma avaliação por imóvel)
            // ==========================================================================
            $table->unique(['property_id', 'user_id']);
        });
    }

    /**
     * Reverte a migration (Destrói a tabela 'reviews').
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
