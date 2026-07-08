<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// ==============================================================================
// MODEL ELOQUENT: UNIVERSIDADE (University)
// ==============================================================================
//
// CONCEITO PARA INICIANTES (O QUE É UM MODEL NO LARAVEL?):
// --------------------------------------------------------
// O Laravel utiliza um padrão de projeto de ORM chamado "Eloquent".
// Em vez de você escrever comandos SQL complexos na mão como:
//   SELECT * FROM universities WHERE city = 'São Paulo';
// 
// Você interage direto com esta classe PHP usando métodos orientados a objetos:
//   University::where('city', 'São Paulo')->get();
//
// Cada instância (objeto) da classe University representa 1 linha lá na nossa 
// tabela 'universities' no banco de dados MySQL!
// ==============================================================================

class University extends Model
{
    // A trait HasFactory permite criarmos dados falsos (fakes) para testes automatizados
    // chamando: University::factory()->create();
    use HasFactory;

    // ==========================================================================
    // 1. PROTEÇÃO CONTRA ATRIBUIÇÃO EM MASSA ($fillable)
    // ==========================================================================
    // SEGURANÇA SÊNIOR — POR QUE ISSO É CRÍTICO?
    // Imagine um formulário onde o usuário pode cadastrar ou editar uma universidade.
    // Se o código fizer um "Mass Assignment" (atribuição em massa):
    //   University::create($request->all());
    //
    // Um hacker malicioso poderia injetar no formulário HTML um campo extra de 
    // sistema (ex: 'is_admin' ou 'id') e tentar tomar controle ou alterar dados protegidos.
    // O array $fillable é uma "lista branca" (whitelist) de segurança: nós dizemos
    // ao Laravel: "APENAS estas colunas abaixo podem ser preenchidas diretamente 
    // por dados vindos de fora". Qualquer outra coluna será ignorada!
    protected $fillable = [
        'name',
        'slug',
        'city',
        'state',
        'latitude',
        'longitude',
    ];

    // ==========================================================================
    // 2. CONVERSÃO DE TIPOS / CASTING ($casts)
    // ==========================================================================
    // No banco MySQL, campos DECIMAL como latitude e longitude costumam ser 
    // devolvidos para o PHP como Strings (texto, ex: "23.55052000").
    // 
    // Quando nossa API REST devolver o JSON para o frontend (React) ou quando
    // formos sincronizar com o Elasticsearch (que exige números reais para geo_point),
    // nós não queremos texto, queremos números float de verdade!
    // A propriedade $casts instrui o Eloquent a converter os valores automaticamente
    // sempre que lermos ou gravarmos o modelo na memória.
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // ==========================================================================
    // 3. RELACIONAMENTOS DO BANCO DE DADOS (Relationships)
    // ==========================================================================
    // AULA SOBRE RELACIONAMENTO 1 PARA MUITOS (One-to-Many):
    // Uma Universidade pode ter ao redor dela VÁRIOS Imóveis cadastrados.
    // Mas cada Imóvel é cadastrado perto de apenas UMA Universidade principal.
    // 
    // No Eloquent, nós representamos esse lado do "Muitos" com o método hasMany().
    // Quando nossa tabela 'properties' (Imóveis) estiver criada na próxima etapa,
    // poderemos pegar todos os imóveis da USP simplesmente chamando:
    //   $usp = University::find(1);
    //   $imoveis = $usp->properties; // Retorna uma coleção de imóveis!
    //
    // (Deixamos o método preparado para a próxima entidade que vamos programar!)
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
