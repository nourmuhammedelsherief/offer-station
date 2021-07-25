<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class Covering extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'covering:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the covering date and delete the oldest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('Check the covering date and delete the oldest');
        $coverings = \App\Covering::whereStatus('1')->get();
        if ($coverings->count() > 0){
            foreach ($coverings as $covering)
            {
                if ($covering->end_date < Carbon::now())
                {
                    $covering->delete();
                }
            }
        }
    }
}
