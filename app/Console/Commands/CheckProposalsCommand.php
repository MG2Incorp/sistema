<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Proposal;

use Carbon\Carbon;

class CheckProposalsCommand extends Command {

    protected $signature = 'CheckProposalsCommand';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('START - Data:'.Carbon::now());

        $proposals = Proposal::where('status', 'RESERVED')->get();

        foreach ($proposals as $key => $proposal) {
            $this->info('PROPOSAL CHECKED: '.$proposal->id);

            $hours = $proposal->property->block->building->project->expiration_time;

            $first = Carbon::parse($proposal->created_at)->addHours($hours);
            $second = Carbon::now();

            if ($first->lessThan($second)) {
                $proposal->status = "CANCELED";
                $proposal->save();

                $this->info('PROPOSAL EXPIRED: '.$proposal->id);

                $queue1 = Proposal::where('property_id', $proposal->property_id)->where('status', 'QUEUE_1')->first();
                if ($queue1) {
                    $queue1->status = "RESERVED";
                    $queue1->save();

                    $this->info('PROPOSAL CHANGED TO RESERVED: '.$queue1->id);
                }

                $queue2 = Proposal::where('property_id', $proposal->property_id)->where('status', 'QUEUE_2')->first();
                if ($queue2) {
                    $queue2->status = "QUEUE_1";
                    $queue2->save();

                    $this->info('PROPOSAL CHANGED TO QUEUE_1: '.$queue2->id);
                }
            }
        }

        $this->info('END - Data:'.Carbon::now());
    }
}
