<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test {

    /* CLASS FOR TESTS */

    private $billings = [
        [ "payment_id" => 122, "month" => 10, "year" => 2019, "expires_at" => "2019-10-23", "token" => "1571685676ESSI5321", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],

        [ "payment_id" => 123, "month" => 10, "year" => 2019, "expires_at" => "2019-10-23", "token" => "1571685676ESSI5346", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 11, "year" => 2019, "expires_at" => "2019-11-23", "token" => "1571685676ESSI539S", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 12, "year" => 2019, "expires_at" => "2019-12-23", "token" => "157168567678NQZMIK", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 1,  "year" => 2020, "expires_at" => "2020-01-23", "token" => "1571685676EU72SWV6", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 2,  "year" => 2020, "expires_at" => "2020-02-23", "token" => "1571685676PXC95UDO", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 3,  "year" => 2020, "expires_at" => "2020-03-23", "token" => "1571685676UO089BUQ", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 4,  "year" => 2020, "expires_at" => "2020-04-23", "token" => "2571685676ESSI5346", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 5,  "year" => 2020, "expires_at" => "2020-05-23", "token" => "3771685676ESSI539S", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 6,  "year" => 2020, "expires_at" => "2020-06-23", "token" => "487168567678NQZMIK", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 7,  "year" => 2020, "expires_at" => "2020-07-23", "token" => "9971685676EU72SWV6", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 8,  "year" => 2020, "expires_at" => "2020-08-23", "token" => "7871685676PXC95UDO", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 9,  "year" => 2020, "expires_at" => "2020-09-23", "token" => "1371685676UO089BUQ", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 10, "year" => 2020, "expires_at" => "2020-10-23", "token" => "1871685676ESSI5346", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 11, "year" => 2020, "expires_at" => "2020-11-23", "token" => "2671685676ESSI539S", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ],
        [ "payment_id" => 123, "month" => 12, "year" => 2020, "expires_at" => "2020-12-23", "token" => "857168567678NQZMIK", "value" => "1200.00", "paid_value" => "0.00", "status" => "PENDING" ]
    ];


    public function generateBillets($proposal, $months, $hoje) {

        $months = 3;
        for ($j = 0 ; $j < 20; $j++) {
            $hoje = \Carbon\Carbon::now()->copy()->addMonths($j)->startOfDay();

            printa("***************************** CENARIO EM ".$hoje." *****************************");

            foreach ($proposal->payments as $key => $payment) {
                // if($payment->method != 'Boleto') continue;

                $primeira_parcela = \Carbon\Carbon::parse($payment->expires_at);

                /* PEGA OS MESES QUE TEM REAJUSTE PARA ENVIAR OS BOLETOS */
                $meses_com_reajustes = [];
                for ($i = 0; $i <= ceil($payment->quantity / $months); $i++) {
                    $aux = $primeira_parcela->copy();
                    $parcela = $aux->addMonths($months*$i);
                    $meses_com_reajustes[] = (string) $parcela->month."-".$parcela->year;
                }

                /* SE NÃO FOR MÊS COM REAJUSTE NÃO FAZ NADA */
                if(!in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) continue;

                $pos = array_search((string) $hoje->month."-".$hoje->year, $meses_com_reajustes);
                /* PEGA O PRÓXIMO MÊS COM REAJUSTE QUE VAI SER O LIMITE PARA ENVIAR OS BOLETOS */
                $end = $pos + 1;

                $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

                if(isset($meses_com_reajustes[$end])) {
                    $explode = explode('-', $meses_com_reajustes[$end]);
                    $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
                    $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
                } else {
                    $billings = \App\Billing::where('payment_id', $payment->id)->where('expires_at', '>', $prazo_inicial)->get();
                }

                if($billings->count()) {
                    foreach($billings as $key => $billing) {
                        printa("PAGAMENTO: ".$payment->id." -- GERAR E ENVIAR BOLETO DA COBRANCA: ".$billing['token']);

                        $plugboleto = new \App\Api\PlugBoleto();

                        /* SE AINDA NÂO EXISTE O BOLETO DA COBRANÇA, TENTA GERAR */
                        if(!$billing->billet_generated()) {
                            $billet = \App\Billet::create([
                                'billetable_type'   => 'App\Billing',
                                'billetable_id'     => $billing->id,
                                'token'             => getBillingToken(8)
                            ]);

                            $boleto = $plugboleto->postBoleto($billing, $billet);

                            if(isset($boleto->_dados->_falha[0]->_erro->erros)) {
                                \App\BilletError::create([
                                    'billet_id' => $billet->id,
                                    'message'   => json_encode($boleto->_dados->_falha[0]->_erro->erros)
                                ]);
                            }

                            if(isset($boleto->_dados->_sucesso[0]->idintegracao)) {
                                $billet->update([
                                    'idIntegracao'  => $boleto->_dados->_sucesso[0]->idintegracao,
                                    'emitted_at'    => \Carbon\Carbon::now()->toDateString(),
                                    'status'        => isset($boleto->_dados->_sucesso[0]->situacao) ? $boleto->_dados->_sucesso[0]->situacao : null,
                                ]);

                                if(isset($boleto->_dados->_sucesso[0]->situacao)) $billet->setStatus($boleto->_dados->_sucesso[0]->situacao);
                            }
                        }
                    }
                }
            }

            printa("<br><br>");
        }
    }

}
