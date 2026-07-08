<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// ==============================================================================
// FACTORY: GERADOR DE UNIVERSIDADES DE TESTE (UniversityFactory)
// ==============================================================================
//
// CONCEITO PARA INICIANTES (O QUE É UMA FACTORY E POR QUE USAR?):
// ----------------------------------------------------------------
// No briefing do nosso projeto, um dos "Desafios de Nível Sênior" é:
//   "Testes que importam: Implemente testes de feature para os endpoints da API...
//    cobertura dos caminhos críticos é essencial."
//
// Para testarmos se o nosso sistema busca imóveis perto da universidade certa
// ou se pagina a lista corretamente, precisamos de DADOS no banco de dados.
// Em vez de inventar dados na mão toda vez que rodamos um teste automatizado,
// a Factory usa uma biblioteca chamada "Faker" para gerar centenas de 
// universidades realistas no banco em menos de 1 segundo!
//
// Exemplo de uso nos testes ou no terminal (Tinker):
//   University::factory()->count(10)->create(); // Cria 10 universidades no MySQL!
// ==============================================================================

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\University>
 */
class UniversityFactory extends Factory
{
    /**
     * O nome do model correspondente a esta factory.
     */
    protected $model = University::class;

    /**
     * Define o estado padrão (as colunas fictícias) do modelo gerado.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Geramos um nome de universidade fictício usando palavras do Faker
        // Ex: "Universidade Federal de São Paulo"
        $name = 'Universidade ' . $this->faker->city();
        
        return [
            // Nome da faculdade
            'name' => $name,
            
            // Transformamos o nome em URL amigável (slug)
            // Ex: Str::slug("Universidade São Paulo") => "universidade-sao-paulo"
            // Adicionamos o ID único do Faker para evitar duplicação em testes em massa
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            
            // Cidade e Estado brasileiros fictícios
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(), // Gera siglas de 2 letras: 'SP', 'RJ', 'MG'
            
            // Coordenadas Geográficas geradas ao redor de coordenadas reais (Brasil)
            // Isso simula localizações reais de GPS para testarmos o cálculo de 
            // distância e boosting geográfico do Elasticsearch mais tarde!
            'latitude' => $this->faker->latitude(-30.0, -0.0), // Faixa de latitude do Brasil
            'longitude' => $this->faker->longitude(-70.0, -35.0), // Faixa de longitude do Brasil
        ];
    }
}
