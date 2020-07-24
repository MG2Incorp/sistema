<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAntecipationsCommand extends Command
{
    protected $signature = 'CheckAntecipationsCommand';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('START - Data:'.\Carbon\Carbon::now());

        $aheads = \App\Ahead::where('status', 'PENDING')->get();

        foreach ($aheads as $key => $ahead) {
            if(!$ahead->billet_generated()) continue;

            $data_maxima = \Carbon\Carbon::parse($ahead->emitted_at)->addDays(7)->startOfDay();
            if($data_maxima < \Carbon\Carbon::now()) {
                /* ULTRAPASSOU A DATA DE ESPERA PARA PAGAMENTO DA ANTECIPAÇÃO */
                $ahead->setStatus('CANCELED');
            }
        }

        $this->info('END - Data:'.\Carbon\Carbon::now());
    }
}
