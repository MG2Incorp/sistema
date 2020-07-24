<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckBilletsCommand extends Command
{
    protected $signature = 'CheckBilletsCommand';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('START - Data:'.\Carbon\Carbon::now());

        $plugboleto = new \App\Api\PlugBoleto();

        $billets = \App\Billet::whereIn('status', [ 'SALVO', 'EMITIDO', 'REGISTRADO', 'BAIXADO' ])->where('billetable_type', '!=', 'App\BillingTest')->get();
        if($billets->count()) {
            foreach ($billets as $key => $billet) {
                \Log::info('Boleto Verificado: '.$billet->token);
                $boleto = $plugboleto->getBoletos($billet);

                if(isset($boleto->_dados[0]->IdIntegracao) && $boleto->_dados[0]->IdIntegracao == $billet->idIntegracao) {

                    // $this->info('Boleto Verificado: '.$billet->token);

                    $billet->update([
                        'status'                => isset($boleto->_dados[0]->situacao) ? $boleto->_dados[0]->situacao : null,
                        'bar_code'              => isset($boleto->_dados[0]->TituloLinhaDigitavel) ? $boleto->_dados[0]->TituloLinhaDigitavel : null,
                        'PagamentoData'         => isset($boleto->_dados[0]->PagamentoData) ? $boleto->_dados[0]->PagamentoData : null,
                        'PagamentoRealizado'    => isset($boleto->_dados[0]->PagamentoRealizado) ? $boleto->_dados[0]->PagamentoRealizado : null,
                        'PagamentoValorPago'    => isset($boleto->_dados[0]->PagamentoValorPago) ? $boleto->_dados[0]->PagamentoValorPago : null,
                    ]);

                    if(!in_array($billet->billetable->status, [ 'PAID', 'PAID_MANUAL' ])) {
                        $billet->billetable->update([ 'paid_value' => isset($boleto->_dados[0]->PagamentoValorPago) ? toCoin($boleto->_dados[0]->PagamentoValorPago) : null ]);
                    }

                    if(isset($boleto->_dados[0]->situacao)) {
                        // \Log::info('Boleto Verificado: '.$billet->token.'SET STATUS ANTES');
                        $billet->setStatus($boleto->_dados[0]->situacao);
                        // \Log::info('Boleto Verificado: '.$billet->token.'SET STATUS DEPOIS');

                        if(!in_array($billet->billetable->status, [ 'PAID', 'PAID_MANUAL' ])) {
                            if($billet->billetable_type == 'App\Ahead') {
                                switch ($boleto->_dados[0]->situacao) {
                                    case 'EMITIDO':     $billet->billetable->setStatus('PENDING');     break;
                                    // case 'REJEITADO':   $billet->billetable->setStatus('CANCELED');    break;
                                    case 'REGISTRADO':  $billet->billetable->setStatus('PENDING');     break;
                                    case 'LIQUIDADO':
                                        $billet->billetable->setStatus('PAID');
                                        try {
                                            $billings = json_decode($billet->billetable->billings);

                                            if($billet->billetable->is_total) {
                                                $proposal = $billet->billetable->payment->proposal;
                                                foreach ($proposal->payments as $key => $payment) {
                                                    if(!$payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->count()) continue;

                                                    $last_value = $billet->billetable->payment->generateAntecipation(1);

                                                    foreach ($billings as $key => $b) {
                                                        if(!$cobranca = \App\Billing::where('id', $b)->where('payment_id', $payment->id)->first()) continue;

                                                        $cobranca->setStatus('PAID');
                                                        $cobranca->update([ 'paid_value' => $last_value ]);

                                                        try {
                                                            if($billet->PagamentoData) {
                                                                $data = dateTimeStringInverse($billet->PagamentoData);
                                                                $cobranca->update([ 'paid_at' => $data ]);
                                                            }
                                                        } catch (\Exception $e) {
                                                            logging($e);
                                                        }

                                                        $last_value = $last_value + ($last_value * $proposal->tax / 100);
                                                    }
                                                }
                                            } else {
                                                $last_value = $billet->billetable->payment->generateAntecipation(1);

                                                foreach ($billings as $key => $b) {
                                                    if(!$cobranca = \App\Billing::find($b)) continue;

                                                    $cobranca->setStatus('PAID');
                                                    $cobranca->update([ 'paid_value' => $last_value ]);

                                                    $last_value = $last_value + ($last_value * $billet->billetable->payment->proposal->tax / 100);
                                                }
                                            }
                                        } catch (\Exception $e) { logging($e); }
                                    break;
                                    case 'BAIXADO':     $billet->billetable->setStatus('PAID_MANUAL'); break;
                                    default: break;
                                }
                            } else {
                                switch ($boleto->_dados[0]->situacao) {
                                    case 'EMITIDO':
                                        if($billet->billetable->status != 'OUTDATED') { /* ALTERAÇÃO FEITA DEVIDO A CONFLITO DE ATUALIZAÇÕES DE STATUS POR CONTA DO CRON QUE TESTA AS COBRANÇAS VENCIDAS */
                                            $billet->billetable->setStatus('PENDING');
                                        }
                                    break;
                                    // case 'REJEITADO':   $billet->billetable->setStatus('CANCELED');    break;
                                    case 'REGISTRADO':
                                        if($billet->billetable->status != 'OUTDATED') { /* ALTERAÇÃO FEITA DEVIDO A CONFLITO DE ATUALIZAÇÕES DE STATUS POR CONTA DO CRON QUE TESTA AS COBRANÇAS VENCIDAS */
                                            $billet->billetable->setStatus('PENDING');
                                        }
                                    break;
                                    case 'LIQUIDADO':   $billet->billetable->setStatus('PAID');        break;
                                    case 'BAIXADO':     $billet->billetable->setStatus('PAID_MANUAL'); break;
                                    default: break;
                                }
                            }
                        }
                    }

                    if(isset($boleto->_dados[0]->motivo) && $boleto->_dados[0]->motivo) {
                        \App\BilletError::create([
                            'billet_id' => $billet->id,
                            'message'   => $boleto->_dados[0]->motivo
                        ]);
                    }
                }
            }
        }

        $billets = \App\Billet::whereIn('status', [ 'SALVO', 'EMITIDO', 'REGISTRADO', 'BAIXADO' ])->where('billetable_type', 'App\BillingTest')->get();

        if($billets->count()) {
            foreach ($billets as $key => $billet) {
                \Log::info('Boleto TESTE Verificado: '.$billet->token);
                $boleto = $plugboleto->getBoletos($billet);

                if(isset($boleto->_dados[0]->IdIntegracao) && $boleto->_dados[0]->IdIntegracao == $billet->idIntegracao) {

                    $billet->update([
                        'status'                => isset($boleto->_dados[0]->situacao) ? $boleto->_dados[0]->situacao : null,
                        'bar_code'              => isset($boleto->_dados[0]->TituloLinhaDigitavel) ? $boleto->_dados[0]->TituloLinhaDigitavel : null,
                        'PagamentoData'         => isset($boleto->_dados[0]->PagamentoData) ? $boleto->_dados[0]->PagamentoData : null,
                        'PagamentoRealizado'    => isset($boleto->_dados[0]->PagamentoRealizado) ? $boleto->_dados[0]->PagamentoRealizado : null,
                        'PagamentoValorPago'    => isset($boleto->_dados[0]->PagamentoValorPago) ? $boleto->_dados[0]->PagamentoValorPago : null,
                    ]);

                    if(isset($boleto->_dados[0]->situacao)) {
                        $billet->setStatus($boleto->_dados[0]->situacao);

                        if($boleto->_dados[0]->situacao == 'EMITIDO' && !$billet->remessa_gerada) {
                            try {
                                $api = new \App\Api\PlugBoleto();
                                $solicitacao = $api->gerarRemessaUnica($billet);
                                // \Log::info(serialize($solicitacao));

                                if(isset($solicitacao->_dados->_sucesso[0]->arquivo) && $solicitacao->_dados->_sucesso[0]->arquivo) {
                                    $billet->update([ 'remessa_gerada' => $solicitacao->_dados->_sucesso[0]->arquivo ]);
                                }
                            } catch (\Exception $e) {
                                logging($e);
                            }
                        }
                    }

                    if(isset($boleto->_dados[0]->motivo) && $boleto->_dados[0]->motivo) {
                        \App\BilletError::create([
                            'billet_id' => $billet->id,
                            'message'   => $boleto->_dados[0]->motivo
                        ]);
                    }
                }
            }
        }

        $this->info('END - Data:'.\Carbon\Carbon::now());
    }
}
