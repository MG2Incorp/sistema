<?php

use Carbon\Carbon;

	function logging($e) {
		Log::info('File: '.$e->getFile().' -- Line: '.$e->getLine().' -- Message: '.$e->getMessage());
	}

	function mask($mask, $str){
        $str = str_replace(" ","", $str);
        for($i = 0; $i < strlen($str); $i++){
            $mask[strpos($mask,"#")] = $str[$i];
        }
        return $mask;
	}

	function getToken($length){
		$token = "";
		$codeAlphabet = '';
		//$codeAlphabet .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		//$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";

		$max = strlen($codeAlphabet); // edited

		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}

		return $token;
	}

	function getBillingToken($length, $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"){
		$token = "";
		$max = strlen($codeAlphabet);

		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}

		return time().$token;
	}

    function printa($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    function toCoin($data) {
		$number = str_replace(',', '.', str_replace('.', '', $data));
		return $number;
    }

    function formatMoney($number, $fractional = true) {
	    if ($fractional) {
	        $number = sprintf('%.2f', $number);
	    }
	    while (true) {
	        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
	        if ($replaced != $number) {
	            $number = $replaced;
	        } else {
	            break;
	        }
	    }
		$number = str_replace(',','.',str_replace('.','-',$number));
		$number = str_replace('-',',',$number);
    	return $number;
	}

	function formatMonetaryCorrectionIndex($number) {
	    return number_format($number, 4, ',', '.');
	}

    function dateTimeString($data) {
		return $data ? Carbon::parse($data)->toDateTimeString() : null;
	}

	function dateString($data) {
		$data = explode(' ', $data)[0];
		return implode('/', array_reverse(explode('-', $data)));
	}

	function dateTimeStringBR($data_completa) {
		$data = explode(' ', $data_completa)[0];
		return implode('/', array_reverse(explode('-', $data))).' '.explode(' ', $data_completa)[1];
	}

	function dateTimeStringInverse($data_completa) {
		$data = explode(' ', $data_completa)[0];
		return implode('-', array_reverse(explode('/', $data))).' '.explode(' ', $data_completa)[1];
    }

    function formatData($data) {
		return implode('/', array_reverse(explode('-', $data)));
	}

	function formatDataWithoutDay($data) {
		$data = explode('-', $data);
		unset($data[2]);
		return implode('/', array_reverse($data));
    }

    function dataToSQL($data) {
		return implode('-', array_reverse(explode('/', $data)));
	}

	function getStates() {
		return ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
	}

	function getProposalStatus() {
		return [
			'RESERVED',
			/* 'PROPOSAL', */
			'DOCUMENTS_PENDING',
			'PROPOSAL_REVIEW',
			'DOCUMENTS_REVIEW',
			'CONTRACT_ISSUE',
			'CONTRACT_AVAILABLE',
			'PENDING_SIGNATURE_CLIENT',
			'PENDING_SIGNATURE_CONSTRUCTOR',
			'SOLD',
			'REFUSED',
			'CANCELED',
			'QUEUE_1',
			'QUEUE_2'
		];
	}

	function getProposalStatusName($status) {
		switch ($status) {
			case 'RESERVED': return 'Reservado'; break;
			/* case 'PROPOSAL': return 'Proposta'; break; */
			case 'DOCUMENTS_PENDING': return 'Documentação pendente'; break;
			case 'PROPOSAL_REVIEW': return 'Proposta em Análise'; break;
			case 'DOCUMENTS_REVIEW': return 'Documentação em Análise'; break;
			case 'CONTRACT_ISSUE': return 'Emissão de Contrato'; break;
			case 'CONTRACT_AVAILABLE': return 'Contrato Disponível'; break;
			case 'PENDING_SIGNATURE_CLIENT': return 'Pendente assinatura do cliente'; break;
			case 'PENDING_SIGNATURE_CONSTRUCTOR': return 'Pendente assinatura da incorporadora'; break;
			case 'SOLD': return 'Vendido'; break;
			case 'REFUSED': return 'Reprovado'; break;
			case 'CANCELED': return 'Cancelado'; break;
			case 'QUEUE_1': return 'Fila de Espera #1'; break;
			case 'QUEUE_2': return 'Fila de Espera #2'; break;
			default: return ''; break;
		}
	}

	function onlyNumber($str){
        return preg_replace('#[^0-9]#', '', $str);
    }

	function getFormFields($type) {
		switch ($type) {
			case 'proponente':
				return [
					'PRINCIPAL' => 'Comprador principal',
					'TIPO_PESSOA' => 'Tipo de Pessoa',
					'DOCUMENTO' => 'CPF/CNPJ',
					'RG' => 'RG',
					'RG_EMISSOR' => 'RG Emissor',
					'RG_UF' => 'RG UF',
					'PROPORCAO' => 'Proporção',
					'NOME' => 'Nome',
					'EMAIL' => 'E-Mail',
					'SEXO' => 'Sexo',
					'NASCIMENTO' => 'Nascimento',
					'TELEFONE' => 'Telefone',
					'CELULAR' => 'Celular',
					'MAE' => 'Mãe',
					'PAI' => 'Pai',
					'NATURALIDADE' => 'Naturalidade',
					'PAIS' => 'País',
					'MORADIA' => 'Moradia',
					'RENDA_BRUTA' => 'Renda Bruta',
					'RENDA_LIQUIDA' => 'Renda Líquida',
					'PROFISSAO' => 'Profissão',
					'CARTORIO' => 'Cartório',
					'ESTADO_CIVIL' => 'Estado Civil',
					'REGIME_CASAMENTO' => 'Regime de Casamento',
					'EMPRESA' => 'Empresa',
					'CNPJ' => 'CNPJ',
					'CARGO' => 'Cargo',
					'ADMISSAO' => 'Admissão',
					'TELEFONE_EMPRESA' => 'Telefone da empresa',
					'CELULAR_EMPRESA' => 'Celular da empresa',
					'CEP_EMPRESA' => 'CEP da empresa',
					'LOGRADOURO_EMPRESA' => 'Logradouro da empresa',
					'NUMERO_EMPRESA' => 'Número da empresa',
					'COMPLEMENTO_EMPRESA' => 'Complemento da empresa',
					'BAIRRO_EMPRESA' => 'Bairro da empresa',
					'CIDADE_EMPRESA' => 'Cidade da empresa',
					'UF_EMPRESA' => 'UF da empresa',
					'CEP_RESIDENCIAL' => 'CEP residencial',
					'LOGRADOURO_RESIDENCIAL' => 'Logradouro residencial',
					'NUMERO_RESIDENCIAL' => 'Número residencial',
					'COMPLEMENTO_RESIDENCIAL' => 'Complemento residencial',
					'BAIRRO_RESIDENCIAL' => 'Bairro residencial',
					'CIDADE_RESIDENCIAL' => 'Cidade residencial',
					'UF_RESIDENCIAL' => 'UF residencial',
					'MESMO_ENDERECO_COBRANCA' => 'Endereço de cobrança'
				];
			break;
			case 'conjuge':
				return [
					'CONJUGE_DOCUMENTO' => 'Documento',
					'CONJUGE_RG' => 'RG',
					'CONJUGE_RG_EMISSOR' => 'RG Emissor',
					'CONJUGE_RG_UF' => 'RG UF',
					'CONJUGE_PROPORCAO' => 'Proporção',
					'CONJUGE_NOME' => 'Nome',
					'CONJUGE_EMAIL' => 'E-Mail',
					'CONJUGE_SEXO' => 'Sexo',
					'CONJUGE_NASCIMENTO' => 'Nascimento',
					'CONJUGE_TELEFONE' => 'Telefone',
					'CONJUGE_CELULAR' => 'Celular',
					'CONJUGE_MAE' => 'Mãe',
					'CONJUGE_PAI' => 'Pai',
					'CONJUGE_NATURALIDADE' => 'Naturalidade',
					'CONJUGE_PAIS' => 'País',
					'CONJUGE_RENDA_BRUTA' => 'Renda Bruta',
					'CONJUGE_RENDA_LIQUIDA' => 'Renda Líquida',
					'CONJUGE_PROFISSAO' => 'Profissão',
					'CONJUGE_CARTORIO' => 'Cartório',
					'CONJUGE_EMPRESA' => 'Empresa',
					'CONJUGE_CNPJ' => 'CNPJ',
					'CONJUGE_CARGO' => 'Cargo',
					'CONJUGE_ADMISSAO' => 'Admissão',
					'CONJUGE_TELEFONE_EMPRESA' => 'Telefone da empresa',
					'CONJUGE_CELULAR_EMPRESA' => 'Celular da empresa',
					'CONJUGE_CEP_EMPRESA' => 'CEP da empresa',
					'CONJUGE_LOGRADOURO_EMPRESA' => 'Logradouro da empresa',
					'CONJUGE_NUMERO_EMPRESA' => 'Número da empresa',
					'CONJUGE_COMPLEMENTO_EMPRESA' => 'Complemento da empresa',
					'CONJUGE_BAIRRO_EMPRESA' => 'Bairro da empresa',
					'CONJUGE_CIDADE_EMPRESA' => 'Cidade da empresa',
					'CONJUGE_UF_EMPRESA' => 'UF da empresa',
				];
			break;
			case 'proposta':
				return [
					'NUMERO_PROPOSTA' => 'Número',
					'MIDIA' => 'Mídia',
					'MOTIVO' => 'Motivo da Compra',
					'PROPOSTA_OBSERVACOES' => 'Observações',
					'DATA_CONTRATO' => 'Data do Contrato',
					'DATA_CONTRATO_EXTENSO' => 'Data do Contrato por Extenso',
					'VALOR_PROPOSTA' => 'Valor da Proposta',
					'VALOR_PROPOSTA_EXTENSO' => 'Valor da Proposta por Extenso',
					'VALOR_DESCONTO' => 'Valor do Desconto',
					'VALOR_DESCONTO_EXTENSO' => 'Valor do Desconto por Extenso',
					'TABELA_COMISSAO' => 'Valor Tabela - Comissão',
					'PROPOSTA_COMISSAO' => 'Valor Proposta - Comissão',
				];
			break;
			case 'pagamento':
				return [
					'MODALIDADE' => 'Modalidade',
					'PAGAMENTOS' => 'Pagamentos',
					'INDICE_FREQUENCIA' => 'Frequência do Índice',
					'INDICE' => 'Índice',
					'VALOR_CORRETAGEM' => 'Valor da Corretagem',
					'VALOR_CORRETAGEM_EXTENSO' => 'Valor da Corretagem por Extenso'
				];
			break;
			case 'empreendimento':
				return [
					'RAZAO_SOCIAL' => 'Razão Social',
					'CNPJ_EMPREENDIMENTO' => 'CNPJ',
					'NOME_EMPREENDIMENTO' => 'Nome',
					'DATA_ENTREGA' => 'Data de entrega',
					'STATUS' => 'Status',
					'LOCAL' => 'Local',
					'TIPO' => 'Tipo',
					'OBSERVACOES' => 'Observações',
					'JUROS' => '% Juros',
					'JUROS_EXTENSO' => '% Juros por Extenso',
				];
			break;
			case 'imovel':
				return [
					'BLOCO' => 'Bloco',
					'ANDAR' => 'Andar',
					'NUMERO' => 'Número',
					'VALOR' => 'Valor',
					'VALOR_EXTENSO' => 'Valor por Extenso',
					'AREA' => 'Área',
					'DIMENSOES' => 'Dimensões',
					'NUMERO_MATRICULA' => 'Número da Matrícula',
					'CADASTRO_IMOBILIARIO' => 'Cadastro Imobiliário',
				];
			break;
			case 'imobiliaria':
				return [
					'IMOBILIARIA_NOME' => 'Nome',
					'IMOBILIARIA_CRECI' => 'CRECI',
					'IMOBILIARIA_CNPJ' => 'CNPJ',
					'IMOBILIARIA_CORRETOR_NOME' => 'Nome do Corretor',
					'IMOBILIARIA_CORRETOR_CRECI' => 'CRECI do Corretor',
					'COMISSAO' => '% Comissão',
					'COMISSAO_EXTENSO' => '% Comissão por Extenso',
				];
			break;
			case 'blocos':
				return [
					'PROPO_1' => 'Bloco - Proponente 1',
					'PROPO_2' => 'Bloco - Proponente 2',
					'PROPO_3' => 'Bloco - Proponente 3',
					'PROPO_4' => 'Bloco - Proponente 4',
					'CONJUGE_1' => 'Bloco - Cônjuge Prop. 1',
					'CONJUGE_2' => 'Bloco - Cônjuge Prop. 2',
					'CONJUGE_3' => 'Bloco - Cônjuge Prop. 3',
					'CONJUGE_4' => 'Bloco - Cônjuge Prop. 4',
				];
			break;
			default:
				# code...
				break;
		}
	}

	function getCorrectionIndexes() {
		return [
			"IGP-M (FGV) Índice Geral de Preços da Fundação Getúlio Vargas",
			"INCC - Índice Nacional de Custos da Construção",
			"IPC-FGV - Índice de Preços ao Consumidor da Fundação Getúlio Vargas",
			"INPC - Índice Nacional de Preços ao Consumidor",
			"IPCA - Índice de Preços ao Consumidor Amplo",
			"IPC-RMSP - Índice de Preços ao Consumidor da Fundação Instituto de Pesquisas Econômicas",
			"IPA - Índice de Preços por Atacado"
		];
	}

	function getPermissions($role) {
		/* ROLES: AGENT, COORDINATOR, INCORPORATOR, ADMIN, ENGINEER */

		/* PROPOSAL_CREATE, PROPOSAL_EDIT, PROPOSAL_DELETE */
		/* PROPERTY_CREATE, PROPERTY_EDIT, PROPERTY_DELETE, PROPERTY_SOLD */
		/* BLOCK_DELETE */
		/* USER_SELECT */
		/* UPDATE_CONSTRUCTION */

		$array = array();
		// $array['AGENT'] = ['PROPOSAL_CREATE', 'PROPOSAL_EDIT', 'PROPOSAL_DELETE'];
		// $array['COORDINATOR'] = ['PROPERTY_CREATE', 'PROPERTY_EDIT', 'PROPERTY_DELETE', 'PROPERTY_SOLD', 'BLOCK_DELETE', 'BLOCK_CREATE'];
		// $array['INCORPORATOR'] = ['PROPERTY_STATUS'];
		// $array['ADMIN'] = ['USER_SELECT'];

		$array['ENGINEER'] = [ 'UPDATE_CONSTRUCTION' ];
		$array['AGENT'] = [ 'PROPOSAL_CREATE', 'PROPOSAL_EDIT', 'PROPOSAL_DELETE' ];
		$array['COORDINATOR'] = [ 'USER_SELECT' ];
		$array['INCORPORATOR'] = [ 'PROPERTY_EDIT', 'PROPERTY_SOLD', 'PROPERTY_STATUS', 'FINANCIAL_MODULE_ACCESS' ];
		$array['ADMIN'] = [ 'PROPERTY_CREATE', 'PROPERTY_DELETE', 'BLOCK_DELETE', 'BLOCK_CREATE' ];

		switch ($role) {
			case 'AGENT': return $array['AGENT']; break;
			case 'ENGINEER': return $array['ENGINEER']; break;
			case 'COORDINATOR': return array_merge($array['AGENT'], $array['COORDINATOR']);
			case 'INCORPORATOR': return array_merge($array['ENGINEER'], $array['AGENT'], $array['COORDINATOR'], $array['INCORPORATOR']);
			case 'ADMIN': return array_merge($array['ENGINEER'], $array['AGENT'], $array['COORDINATOR'], $array['INCORPORATOR'], $array['ADMIN']);
			case 'ALL': return $array;
			default: break;
		}

		return array();
	}

	function getPermissionName($permission) {
		switch ($permission) {
			case 'PROPOSAL_CREATE': 		return 'Criar proposta'; 					break;
			case 'PROPOSAL_EDIT': 			return 'Editar proposta'; 					break;
			case 'PROPOSAL_DELETE': 		return 'Cancelar proposta'; 				break;
			case 'PROPERTY_CREATE': 		return 'Criar imóvel'; 						break;
			case 'PROPERTY_EDIT': 			return 'Editar imóvel'; 					break;
			case 'PROPERTY_DELETE': 		return 'Excluir imóvel'; 					break;
			case 'PROPERTY_SOLD': 			return 'Status vendido'; 					break;
			case 'BLOCK_DELETE': 			return 'Excluir quadra/andar'; 				break;
			case 'BLOCK_CREATE': 			return 'Criar quadra/andar'; 				break;
			case 'COMPANY_CREATE': 			return 'Criar imobiliária'; 				break;
			case 'COMPANY_EDIT': 			return 'Editar imobiliária'; 				break;
			case 'COMPANY_DELETE': 			return 'Excluir imobiliária'; 				break;
			case 'USER_SELECT': 			return 'Selecionar usuários'; 				break;
			case 'PROPERTY_STATUS': 		return 'Bloquear unidade'; 					break;
			case 'UPDATE_CONSTRUCTION': 	return 'Atualizar etapas da construção'; 	break;
			case 'FINANCIAL_MODULE_ACCESS': return 'Acesso ao módulo financeiro'; 		break;
			default: break;
		}
	}

	function getRoles() {
		return [
			'ENGINEER',
			'AGENT',
			'COORDINATOR',
			'INCORPORATOR',
			'ADMIN'
		];
	}

	function getRoleIndex($role) {
		switch ($role) {
			case 'ENGINEER': 		return 0; break;
			case 'AGENT': 			return 0; break;
			case 'COORDINATOR': 	return 2; break;
			case 'INCORPORATOR': 	return 3; break;
			case 'ADMIN': 			return 4; break;
			default: 				return 0; break;
		}
	}

	function getRoleName($role) {
		switch ($role) {
			case 'ENGINEER': 		return 'Engenheiro'; 	break;
			case 'AGENT': 			return 'Corretor'; 		break;
			case 'COORDINATOR': 	return 'Coordenador'; 	break;
			case 'INCORPORATOR': 	return 'Incorporador'; 	break;
			case 'ADMIN': 			return 'Administrador'; break;
			default: 				return 0; 				break;
		}
	}

	function getMonths() {
		return [
			'01' => 'Janeiro',
			'02' => 'Fevereiro',
			'03' => 'Março',
			'04' => 'Abril',
			'05' => 'Maio',
			'06' => 'Junho',
			'07' => 'Julho',
			'08' => 'Agosto',
			'09' => 'Setembro',
			'10' => 'Outubro',
			'11' => 'Novembro',
			'12' => 'Dezembro'
		];
	}

	function getMonths2() {
		return [
			1  => 'Janeiro',
			2  => 'Fevereiro',
			3  => 'Março',
			4  => 'Abril',
			5  => 'Maio',
			6  => 'Junho',
			7  => 'Julho',
			8  => 'Agosto',
			9  => 'Setembro',
			10 => 'Outubro',
			11 => 'Novembro',
			12 => 'Dezembro'
		];
	}

	function getMonthsAbbr() {
		return [
			'01' => 'JAN',
			'02' => 'FEV',
			'03' => 'MAR',
			'04' => 'ABR',
			'05' => 'MAI',
			'06' => 'JUN',
			'07' => 'JUL',
			'08' => 'AGO',
			'09' => 'SET',
			'10' => 'OUT',
			'11' => 'NOV',
			'12' => 'DEZ'
		];
	}

	function getMonthsAbbr2() {
		return [
			1  => 'JAN',
			2  => 'FEV',
			3  => 'MAR',
			4  => 'ABR',
			5  => 'MAI',
			6  => 'JUN',
			7  => 'JUL',
			8  => 'AGO',
			9  => 'SET',
			10 => 'OUT',
			11 => 'NOV',
			12 => 'DEZ'
		];
	}

	function getStatusByRole($role) {
		// return [
		// 	'RESERVED',
		// 	'DOCUMENTS_PENDING',
		// 	'PROPOSAL_REVIEW',
		// 	'DOCUMENTS_REVIEW',
		// 	'CONTRACT_ISSUE',
		// 	'CONTRACT_AVAILABLE',
		// 	'PENDING_SIGNATURE_CLIENT',
		// 	'PENDING_SIGNATURE_CONSTRUCTOR',
		// 	'SOLD',
		// 	'REFUSED',
		// 	'CANCELED',
		// 	'QUEUE_1',
		// 	'QUEUE_2'
		// ];

		switch ($role) {
			case 'AGENT':
				return [
					'DOCUMENTS_PENDING',
					'CANCELED',
				];
			break;
			case 'COORDINATOR':
				return [
					'DOCUMENTS_PENDING',
					'CANCELED',
				];
			break;
			case 'INCORPORATOR':
				return [
					'RESERVED',
					'PROPOSAL_REVIEW',
					'DOCUMENTS_PENDING',
					'DOCUMENTS_REVIEW',
					'CONTRACT_ISSUE',
					'CONTRACT_AVAILABLE',
					'PENDING_SIGNATURE_CLIENT',
					'PENDING_SIGNATURE_CONSTRUCTOR',
					'SOLD',
					'REFUSED',
					'CANCELED',
					'QUEUE_1',
					'QUEUE_2'
				];
			break;
			case 'ADMIN':
				return [
					'PROPOSAL_REVIEW',
					'RESERVED',
					'DOCUMENTS_PENDING',
					'DOCUMENTS_REVIEW',
					'CONTRACT_ISSUE',
					'CONTRACT_AVAILABLE',
					'PENDING_SIGNATURE_CLIENT',
					'PENDING_SIGNATURE_CONSTRUCTOR',
					'SOLD',
					'REFUSED',
					'CANCELED',
					'QUEUE_1',
					'QUEUE_2'
				];
			break;
			default: break;
		}
	}

	function convert_number_to_words($number) {
		$hyphen      = '-';
		$conjunction = ' e ';
		// $separator   = ', ';
		$separator   = ' ';
		$negative    = 'menos ';
		// $decimal     = ' ponto ';
		$decimal     = ' ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'um',
			2                   => 'dois',
			3                   => 'três',
			4                   => 'quatro',
			5                   => 'cinco',
			6                   => 'seis',
			7                   => 'sete',
			8                   => 'oito',
			9                   => 'nove',
			10                  => 'dez',
			11                  => 'onze',
			12                  => 'doze',
			13                  => 'treze',
			14                  => 'quatorze',
			15                  => 'quinze',
			16                  => 'dezesseis',
			17                  => 'dezessete',
			18                  => 'dezoito',
			19                  => 'dezenove',
			20                  => 'vinte',
			30                  => 'trinta',
			40                  => 'quarenta',
			50                  => 'cinquenta',
			60                  => 'sessenta',
			70                  => 'setenta',
			80                  => 'oitenta',
			90                  => 'noventa',
			100                 => 'cento',
			200                 => 'duzentos',
			300                 => 'trezentos',
			400                 => 'quatrocentos',
			500                 => 'quinhentos',
			600                 => 'seiscentos',
			700                 => 'setecentos',
			800                 => 'oitocentos',
			900                 => 'novecentos',
			1000                => 'mil',
			1000000             => array('milhão', 'milhões'),
			1000000000          => array('bilhão', 'bilhões'),
			1000000000000       => array('trilhão', 'trilhões'),
			1000000000000000    => array('quatrilhão', 'quatrilhões'),
			1000000000000000000 => array('quinquilhão', 'quinquilhões')
		);

		if (!is_numeric($number)) {
			return false;
		}

		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			trigger_error( 'convert_number_to_words só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX, E_USER_WARNING );
			return false;
		}

		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}

		$string = $fraction = null;

		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $conjunction . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = floor($number / 100)*100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				if ($baseUnit == 1000) {
					$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[1000];
				} elseif ($numBaseUnits == 1) {
					$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
				} else {
					$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
				}
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}

		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}

		return $string;
	}

	function getBanks() {
		return [
			"001" => "BB",
			"003" => "BASA",
			"004" => "BNB",
			"021" => "BANESTE",
			"033" => "SANTANDER",
			"037" => "BANPARA",
			"041" => "BANRISUL",
			"047" => "BANESE",
			"070" => "BRB",
			"084" => "UNIPRIME NORTE DO PARANÁ",
			"085" => "CECRED",
			"089" => "CREDISAN",
			"104" => "CAIXA",
			"237" => "BRADESCO",
			"291" => "BCN",
			"341" => "ITAU",
			"389" => "BMB",
			"422" => "SAFRA",
			"707" => "DAYCOVAL",
			"745" => "CITIBANK",
			"748" => "SICREDI",
			"755" => "BANK OF AMERICA",
			"756" => "SICOOB"
		];
	}

	function getBankCode($code) {
		switch($code) {
			// case '041': return 'Banco Banrisul'; 	break;
			// case '033': return 'Banco Santander'; 	break;
			// case '422': return 'Banco Safra'; 		break;
			// case '001': return 'Banco do Brasil'; 	break;
			// case '756': return 'Sicoob'; 			break;
			// case '104': return 'Banco Caixa'; 		break;
			// case '748': return 'Sicredi'; 			break;
			// case '237': return 'Banco Bradesco'; 	break;
			// case '341': return 'Banco Itaú'; 		break;

			case "001": return "BB"; 						break;
			case "003": return "BASA"; 						break;
			case "004": return "BNB"; 						break;
			case "021": return "BANESTE"; 					break;
			case "033": return "SANTANDER"; 				break;
			case "037": return "BANPARA"; 					break;
			case "041": return "BANRISUL"; 					break;
			case "047": return "BANESE"; 					break;
			case "070": return "BRB"; 						break;
			case "084": return "UNIPRIME NORTE DO PARANÁ"; 	break;
			case "085": return "CECRED"; 					break;
			case "089": return "CREDISAN"; 					break;
			case "104": return "CAIXA"; 					break;
			case "237": return "BRADESCO"; 					break;
			case "291": return "BCN"; 						break;
			case "341": return "ITAU"; 						break;
			case "389": return "BMB"; 						break;
			case "422": return "SAFRA"; 					break;
			case "707": return "DAYCOVAL"; 					break;
			case "745": return "CITIBANK"; 					break;
			case "748": return "SICREDI"; 					break;
			case "755": return "BANK OF AMERICA"; 			break;
			case "756": return "SICOOB"; 					break;

			default: 	return '';
		}
	}

	function getContractStatusLayout($status) {
		switch ($status) {
			case 'FINISH': 		return [ 'content' => 'Finalizado', 	'text' => 'success', 	'badge' => '', 'icon' => 'far fa-check-circle' 			]; break;
			case 'ON_DAY':		return [ 'content' => 'Em dia', 		'text' => 'warning',	'badge' => '', 'icon' => 'far fa-check-circle' 			]; break;
			case 'OVERDUE': 	return [ 'content' => 'Inadimplente', 	'text' => 'danger', 	'badge' => '', 'icon' => 'fas fa-exclamation-circle' 	]; break;
			case 'CANCELED': 	return [ 'content' => 'Cancelado', 		'text' => 'danger', 	'badge' => '', 'icon' => 'fas fa-exclamation-circle' 	]; break;
			default: 			return [ 'content' => $status, 			'text' => 'secondary', 	'badge' => '', 'icon' => '' 							]; break;
		}
	}

	function getBillingStatusLayout($status) {
		switch ($status) {
			case 'PENDING': 	return [ 'content' => 'Pendente', 		'text' => 'warning', 	'badge' => '', 'icon' => 'fas fa-clock' 				]; break;
			case 'PAID':		return [ 'content' => 'Pago', 			'text' => 'success',	'badge' => '', 'icon' => 'fas fa-check-circle' 			]; break;
			case 'CANCELED': 	return [ 'content' => 'Cancelado', 		'text' => 'danger', 	'badge' => '', 'icon' => 'fas fa-exclamation-circle' 	]; break;
			case 'OUTDATED': 	return [ 'content' => 'Vencido', 		'text' => 'danger', 	'badge' => '', 'icon' => 'fas fa-exclamation-circle' 	]; break;
			case 'PAID_MANUAL':	return [ 'content' => 'Baixa Manual',	'text' => 'success', 	'badge' => '', 'icon' => 'fas fa-check-circle' 			]; break;
			default: 			return [ 'content' => $status, 			'text' => 'secondary', 	'badge' => '', 'icon' => '' 							]; break;
		}
	}
?>