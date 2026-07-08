<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// ==============================================================================
// MODEL ELOQUENT: IMÓVEL (Property)
// ==============================================================================
//
// CONCEITO PARA INICIANTES (RELACIONAMENTOS PERTENCE A — BelongsTo):
// ------------------------------------------------------------------
// No model anterior (University), nós vimos que uma faculdade "tem muitos"
// imóveis (hasMany).
// 
// Aqui no imóvel, nós olhamos o lado inverso da moeda: cada Imóvel individual
// "pertence a" (belongsTo) uma Universidade e também "pertence a" um Proprietário
// (User).
//
// Na prática do dia a dia, quando você quiser saber o nome da faculdade de 
// um kitnet específico na API, basta acessar como se fosse uma propriedade PHP:
//   $imovel = Property::find(1);
//   echo $imovel->university->name; // Consulta automática e elegante via ORM!
// ==============================================================================

class Property extends Model
{
    use HasFactory;

    // ==========================================================================
    // 1. LISTA BRANCA DE PROTEÇÃO ($fillable)
    // ==========================================================================
    // Protege nossa aplicação contra injeção indevida de colunas em cadastros.
    protected $fillable = [
        'university_id',
        'user_id',
        'title',
        'slug',
        'description',
        'address',
        'city',
        'state',
        'price',
        'bedrooms',
        'bathrooms',
        'distance_to_university',
        'latitude',
        'longitude',
        'is_available',
    ];

    // ==========================================================================
    // 2. CONVERSÃO AUTOMÁTICA DE TIPOS ($casts)
    // ==========================================================================
    // Quando nossa API retornar este imóvel para o frontend em React (TypeScript),
    // queremos garantir os tipos corretos:
    // - price, latitude e longitude como floats (números reais).
    // - distance_to_university, bedrooms e bathrooms como inteiros (number no TS).
    // - is_available como booleano (true/false em vez de 1 e 0 do MySQL).
    protected $casts = [
        'price' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'distance_to_university' => 'integer',
        'is_available' => 'boolean',
    ];

    // ==========================================================================
    // 3. RELACIONAMENTOS COM OUTRAS TABELAS
    // ==========================================================================
    
    /**
     * O Imóvel pertence a uma Universidade (Pai).
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /**
     * O Imóvel pertence a um Proprietário / Usuário (Pai).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * PREPARAÇÃO PARA A PRÓXIMA ETAPA (Avaliações):
     * Um Imóvel poderá receber várias avaliações (limpeza, localização, custo-benefício)
     * feitas pelos estudantes.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
