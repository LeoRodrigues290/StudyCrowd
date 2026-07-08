<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ==============================================================================
// MODEL ELOQUENT: AVALIAÇÃO (Review)
// ==============================================================================
//
// AULA DE ARQUITETURA SÊNIOR — EVENTOS DO MODELO (Model Lifecycle Hooks):
// -----------------------------------------------------------------------
// O briefing exige que a nota média (overall_score) seja calculada a partir 
// das 3 notas individuais: limpeza, localização e custo-benefício.
// 
// Em vez de obrigar todo programador da equipe a lembrar de fazer essa conta
// matemática lá no Controller antes de salvar:
//   $media = ($req->limpeza + $req->loc + $req->custo) / 3;
//
// Nós usamos um Evento do Eloquent no método booted() chamado `saving()`.
// Sempre que o Laravel for criar ou editar uma avaliação no MySQL, ele executará
// essa função matemática AUTOMATICAMENTE nos bastidores! Isso garante consistência
// de dados 100% à prova de erros humanos em toda a aplicação!
// ==============================================================================

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'cleanliness_score',
        'location_score',
        'value_score',
        'overall_score',
        'comment',
    ];

    protected $casts = [
        'cleanliness_score' => 'integer',
        'location_score' => 'integer',
        'value_score' => 'integer',
        'overall_score' => 'float',
    ];

    // ==========================================================================
    // 1. HOOK DE SISTEMA — CÁLCULO AUTOMÁTICO DA MÉDIA
    // ==========================================================================
    protected static function booted(): void
    {
        static::saving(function (Review $review) {
            // Soma as 3 notas e divide por 3, arredondando para 2 casas decimais.
            // Ex: (5 + 4 + 5) / 3 = 14 / 3 = 4.6666 => 4.67
            $review->overall_score = round(
                ($review->cleanliness_score + $review->location_score + $review->value_score) / 3,
                2
            );
        });
    }

    // ==========================================================================
    // 2. RELACIONAMENTOS COM OUTRAS TABELAS
    // ==========================================================================

    /**
     * A avaliação pertence a um Imóvel.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * A avaliação foi escrita por um Usuário / Estudante.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
