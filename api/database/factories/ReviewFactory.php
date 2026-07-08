<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

// ==============================================================================
// FACTORY: GERADOR DE AVALIAÇÕES DE TESTE (ReviewFactory)
// ==============================================================================
//
// AULA DE TESTES AUTOMATIZADOS — GERAÇÃO DE NOTAS REALISTAS:
// ----------------------------------------------------------
// Para testarmos nossa aplicação, geramos avaliações com notas aleatórias entre
// 1 e 5 para limpeza, localização e custo-benefício.
// 
// Uma lição incrível de integração Laravel: mesmo que nós coloquemos aqui na 
// factory uma nota 'overall_score' temporária, assim que o método ->create() for
// acionado, o nosso Hook saving() do Model Review vai interceptar e calcular 
// a média matemática exata no MySQL!
// ==============================================================================

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $cleanliness = $this->faker->numberBetween(3, 5); // Tendência a notas positivas
        $location = $this->faker->numberBetween(2, 5);
        $value = $this->faker->numberBetween(3, 5);

        return [
            // Resolução automática das chaves estrangeiras
            'property_id' => Property::factory(),
            'user_id' => User::factory(),

            // Notas por categoria
            'cleanliness_score' => $cleanliness,
            'location_score' => $location,
            'value_score' => $value,

            // Média inicial (o Hook saving do Model garantirá a precisão de 2 casas decimais)
            'overall_score' => round(($cleanliness + $location + $value) / 3, 2),

            // Comentário de estudante universitário
            'comment' => $this->faker->boolean(80) ? $this->faker->paragraph() : null,
        ];
    }
}
