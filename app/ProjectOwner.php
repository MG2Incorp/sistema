<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectOwner extends Model {

    use SoftDeletes;

    protected $table = 'project_owners';

    protected $fillable = [
        'project_id',
        'owner_id',
        'account_id',
        'TituloDocEspecie',
        'TituloDataDesconto',
        'TituloCodDesconto',
        'TituloValorDescontoTaxa',
        'TituloDataJuros',
        'TituloCodigoJuros',
        'TituloValorJuros',
        'TituloDataMulta',
        'TituloCodigoMulta',
        'TituloValorMultaTaxa',
        'TituloCodProtesto',
        'TituloPrazoProtesto',
        'TituloCodBaixaDevolucao',
        'TituloPrazoBaixa',
        'TituloAceite',
        'TituloLocalPagamento',
        'TituloCodEmissaoBloqueto'
    ];
}
