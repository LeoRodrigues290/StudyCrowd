<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// ==============================================================================
// FACTORY: GERADOR DE IMÓVEIS DE TESTE (PropertyFactory)
// ==============================================================================
//
// AULA DE TESTES AUTOMATIZADOS — RESOLUÇÃO AUTOMÁTICA DE CHAVES ESTRANGEIRAS:
// -----------------------------------------------------------------------------
// Como o Imóvel depende obrigatoriamente de uma Universidade e de um Usuário,
// como fazemos nos testes automatizados para criar um imóvel sem dar erro no banco?
//
// Colocamos nos campos 'university_id' e 'user_id' a chamada: University::factory()
// O Laravel é extremamente inteligente: quando você rodar no terminal ou teste:
//   Property::factory()->create();
// Ele percebe a dependência, vai no banco, cria 1 faculdade nova, cria 1 usuário novo,
// pega os IDs deles e injeta no nosso Imóvel! 
// 
// E se você já tiver uma faculdade específica e quiser criar 5 imóveis nela?
// Basta sobrescrever o atributo:
//   Property::factory()->count(5)->create(['university_id' => $usp->id]);
// ==============================================================================

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        // Tipos de imóveis comuns ao redor de universidades
        $types = ['Kitnet reformada', 'Apartamento compartilhado', 'Stoodi Studio', 'Quarto individual em República', 'Flat moderno'];
        
        // Geramos um título realista para universitários
        $title = $this->faker->randomElement($types) . ' ' . $this->faker->streetName();
        
        return [
            // Resolução automática de relacionamentos (Chaves Estrangeiras)
            'university_id' => University::factory(),
            'user_id' => User::factory(),
            
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraphs(3, true),
            
            // Endereço e localização
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            
            // Preço do aluguel universitário (entre R$ 500,00 e R$ 3.500,00)
            'price' => $this->faker->randomFloat(2, 500, 3500),
            
            // Especificações do imóvel
            'bedrooms' => $this->faker->numberBetween(1, 3),
            'bathrooms' => $this->faker->numberBetween(1, 2),
            
            // Distância a pé até a faculdade (entre 100 metros e 4.500 metros)
            'distance_to_university' => $this->faker->numberBetween(100, 4500),
            
            // Coordenadas geográficas ao redor do Brasil
            'latitude' => $this->faker->latitude(-30.0, -0.0),
            'longitude' => $this->faker->longitude(-70.0, -35.0),
            
            // 90% de chance de estar disponível para locação
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
