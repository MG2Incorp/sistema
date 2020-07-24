<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mailer;

class CheckBillingsCommand extends Command
{
    protected $signature = 'CheckBillingsCommand';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('START - Data:'.\Carbon\Carbon::now());

        $billings = \App\Billing::where('status', 'PENDING')->get();

        foreach ($billings as $key => $billing) {

            $data_maxima = \Carbon\Carbon::parse($billing->expires_at)->addDays(4)->startOfDay();
            if($data_maxima < \Carbon\Carbon::now()) {
                /* ULTRAPASSOU A DATA DE VENCIMENTO DA COBRANÇA */
                \Log::info('COBRANÇA VENCIDA: '.$billing->id);
                $billing->setStatus('OUTDATED');

                try {
                    $mailer = new Mailer();
                    $mailer->sendMailOutdatedBilling(@$billing->payment->proposal->main_proponent->email, $billing->payment->proposal, @$billing->payment->proposal->main_proponent);
                } catch (\Exception $e) {
                    logging($e);
                }
            }
        }

        $this->info('END - Data:'.\Carbon\Carbon::now());
    }
}
