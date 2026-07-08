<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==============================================================================
// MIGRATION: CRIAR TABELA DE IMÓVEIS (properties)
// ==============================================================================
//
// AULA DE ARQUITETURA SÊNIOR — CHAVES ESTRANGEIRAS E ÍNDICES COMPOSTOS:
// ----------------------------------------------------------------------
// 1. Integridade Referencial (Chaves Estrangeiras - Foreign Keys):
//    Um Imóvel não flutua no vazio; ele pertence a um Proprietário (user_id)
//    e fica próximo a uma Universidade (university_id).
//    Usamos 'constrained()' para obrigar o MySQL a validar que o ID informado
//    realmente existe na tabela pai. E usamos 'cascadeOnDelete()' para que, 
//    se a faculdade for deletada, seus imóveis sejam limpos automaticamente!
//
// 2. O Desafio de Otimização do Briefing (Índices Compostos):
//    O briefing exige: "Adicione índices compostos nas colunas mais filtradas".
//    Por que um Índice Composto ['university_id', 'is_available', 'price']?
//    Quando o estudante entra na página da USP e filtra:
//      WHERE university_id = 1 AND is_available = 1 ORDER BY price ASC;
//    Um índice simples só em university_id faria o MySQL pegar todos os 5.000 
//    imóveis da USP e depois ter que ordená-los na memória RAM.
//    Com o Índice Composto, o MySQL já guarda no B-Tree as 3 informações juntas
//    e ordenadas, respondendo em microssegundos com custo de CPU zero!
// ==============================================================================

return new class extends Migration
{
    /**
     * Executa a migration (Cria a estrutura da tabela no MySQL).
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            // 1. Chave Primária
            $table->id();

            // 2. Chaves Estrangeiras (Relacionamentos)
            // foreignId('university_id') cria um BIGINT UNSIGNED que aponta para 'universities.id'
            $table->foreignId('university_id')
                  ->constrained('universities')
                  ->cascadeOnDelete();

            // O proprietário do imóvel (aponta para 'users.id')
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // 3. Dados Básicos do Imóvel
            // Título (ex: "Kitnet reformada com Wi-Fi a 2 blocos da USP")
            $table->string('title');
            
            // Slug único para URL amigável (ex: "kitnet-reformada-usp-102")
            $table->string('slug')->unique();
            
            // Descrição longa com detalhes do imóvel
            $table->text('description');

            // 4. Endereço e Localização
            $table->string('address');
            $table->string('city');
            $table->string('state', 2);

            // 5. Especificações do Imóvel e Preço
            // decimal('price', 10, 2) suporta até R$ 99.999.999,99 com 2 casas decimais.
            // NUNCA guarde valores monetários (dinheiro) em colunas FLOAT no MySQL, pois 
            // números flutuantes perdem precisão matemática em somas e juros!
            $table->decimal('price', 10, 2);
            
            // tinyInteger unsigned gasta apenas 1 byte (suporta números de 0 a 255).
            // Otimização de espaço em disco e memória RAM!
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);

            // 6. Distância até a Faculdade
            // Guardamos a distância em METROS (ex: 450 metros).
            // Por que em metros (inteiro) e não em quilômetros (float)?
            // Comparações de inteiros (ex: WHERE distance_to_university <= 1000)
            // são processadas infinitamente mais rápidas pelos processadores do banco!
            $table->unsignedInteger('distance_to_university');

            // 7. Coordenadas GPS (Para sincronizar com Elasticsearch depois)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // 8. Status de Disponibilidade
            $table->boolean('is_available')->default(true);

            // 9. Timestamps do Laravel (created_at e updated_at)
            $table->timestamps();

            // ==========================================================================
            // ÍNDICE COMPOSTO DE ALTA PERFORMANCE (Desafio Sênior)
            // ==========================================================================
            // Cria uma árvore de busca rápida combinando Faculdade + Disponibilidade + Preço.
            $table->index(['university_id', 'is_available', 'price']);
            
            // Índice adicional para buscas gerais por cidade e status
            $table->index(['city', 'is_available']);
        });
    }

    /**
     * Reverte a migration (Destrói a tabela 'properties').
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
