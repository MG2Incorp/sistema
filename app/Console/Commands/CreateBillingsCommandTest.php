<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateBillingsCommandTest extends Command
{
    protected $signature = 'CreateBillingsCommandTest';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('START - Data:'.\Carbon\Carbon::now());

        //$today = $hoje = \Carbon\Carbon::now()->startOfDay();
        $today = $hoje = \Carbon\Carbon::create(2019, 11, 30)->startOfDay();

        $proposals = \App\Proposal::where('status', 'SOLD')->where('id', 232)->get();

        if(!$proposals->count()) return;

        foreach ($proposals as $key => $proposal) {

            foreach ($proposal->proponents as $key => $proponent) {
                $client = \App\Client::firstOrCreate(
                    [ 'document' => onlyNumber($proponent->document) ],
                    [ 'password' => \Hash::make(onlyNumber(formatData($proponent->birthdate))), 'name' => $proponent->name ]
                );

                $proponent->update([ 'client_id' => $client->id ]);

                if($proponent->proponent) {
                    $client = \App\Client::firstOrCreate(
                        [ 'document' => onlyNumber($proponent->proponent->document) ],
                        [ 'password' => \Hash::make(onlyNumber(formatData($proponent->proponent->birthdate))), 'name' => $proponent->proponent->name ]
                    );

                    $proponent->proponent->update([ 'client_id' => $client->id ]);
                }
            }

            if(!$proposal->payments->count()) continue;
            if(!$proposal->property->account_id) continue;

            $periodo = $proposal->correction_type;

            if($periodo) {
                switch ($periodo) {
                    case 'Anual':           $months = 12;    break;
                    case 'Semestral':       $months = 6;     break;
                    case 'Trimestral':      $months = 3;     break;
                    case 'Bimestral':       $months = 2;     break;
                    case 'Mensal':          $months = 1;     break;
                    default:                continue;
                }
            } else {
                $months = 1;
            }

            $indice_atual = $proposal->getCurrentIndex($months, $hoje);
            if($indice_atual < 0) continue;

            //$proposal->generateBilling($months, $indice_atual, $hoje);

            switch ($proposal->property->block->building->project->send_billets) {
                case 'MES':
                    $proposal->generateBillets($hoje);
                break;
                case 'CICLO':
                    $proposal->generateBilletsGroup($months, $hoje);
                break;
                default: \Log::info('OPCAO PARA GERACAO DE BOLETOS INVALIDA'); break;
            }

            continue;

            // $proposal->generateAmortization();
        }

        $this->info('END - Data:'.\Carbon\Carbon::now());
    }
}
