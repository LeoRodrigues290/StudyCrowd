<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ==============================================================================
// MIGRATION: CRIAR TABELA DE UNIVERSIDADES (universities)
# ==============================================================================
//
// CONCEITO PARA INICIANTES (O QUE É UMA MIGRATION?):
// ---------------------------------------------------
// Imagine a Migration como o "controle de versão" (Git) do seu Banco de Dados.
// Em vez de você abrir um programa gráfico (como TablePlus) e criar a tabela na
// mão clicando com o mouse, você escreve em código PHP como a tabela deve ser.
// 
// Vantagem: Se outro desenvolvedor entrar no time ou se você deletar o banco,
// basta rodar 'php artisan migrate' e toda a estrutura renasce idêntica!
//
// FILOSOFIA SÊNIOR — POR QUE CRIAR A TABELA DE UNIVERSIDADES PRIMEIRO?
// --------------------------------------------------------------------
// Em bancos relacionais (MySQL), existe o conceito de Integridade Referencial.
// No nosso negócio, um Imóvel (Property) precisará estar vinculado a uma Universidade
// (ex: "Kitnet perto da USP"). 
// A tabela que é apontada (Universidade - Pai) DEVE existir no banco de dados ANTES
// da tabela que aponta para ela (Imóvel - Filho). Se tentarmos criar a tabela de 
// imóveis primeiro com uma chave estrangeira 'university_id', o MySQL dará erro!
// ==============================================================================

return new class extends Migration
{
    /**
     * Executa a migration (Cria ou altera estruturas no banco de dados).
     * Chamado quando rodamos: 'php artisan migrate'
     */
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            // 1. Chave Primária (Primary Key)
            // Cria um campo 'id' do tipo BIGINT UNSIGNED AUTO_INCREMENT.
            // É o identificador único de cada universidade no sistema.
            $table->id();

            // 2. Nome da Universidade
            // string() no Laravel equivale a VARCHAR(255) no MySQL.
            // Ex: "Universidade de São Paulo (USP)"
            $table->string('name');

            // 3. Slug (URL Amigável para SEO)
            // Ex: "universidade-de-sao-paulo-usp"
            // Por que 'unique()'? Não podem existir duas URLs iguais no site.
            // Por que 'index()'? O briefing pede otimização de queries! Quando o usuário
            // acessar nosso site pelo link do slug, o MySQL achará o registro em 
            // microssegundos se houver um Índice (Index), sem ler a tabela inteira (Table Scan).
            $table->string('slug')->unique();

            // 4. Localização (Cidade e Estado)
            // Adicionamos um index() na cidade porque uma das buscas mais comuns
            // do nosso sistema será filtrar universidades e imóveis por cidade!
            $table->string('city')->index();
            
            // Estado com 2 caracteres (ex: 'SP', 'RJ', 'MG').
            $table->string('state', 2);

            // 5. Coordenadas Geográficas (Latitude e Longitude)
            // PENSAMENTO DE ARQUITETURA SÊNIOR (Olhando para o futuro — Fase 2):
            // Por que colocar latitude e longitude no MySQL agora, na Fase 1?
            // Porque no briefing temos o desafio: "busca full-text e decaimento por 
            // distância geográfica no Elasticsearch (geo_point)". 
            // Para indexarmos a geolocalização lá no Elasticsearch depois, o nosso MySQL
            // precisa ser a fonte da verdade de onde a universidade fica no mapa!
            // Usamos decimal(10, 8) e (11, 8) que é o padrão de precisão de GPS no MySQL.
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // 6. Timestamps Automáticos
            // Cria duas colunas: 'created_at' e 'updated_at' (DATETIME).
            // O próprio Eloquent (Laravel) gerencia essas datas ao criar ou editar um registro.
            $table->timestamps();
        });
    }

    /**
     * Reverte a migration (Desfaz o que o método up() fez).
     * Chamado quando rodamos: 'php artisan migrate:rollback'
     * 
     * Se der algo errado ou quisermos voltar atrás, destruímos a tabela 'universities'.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
