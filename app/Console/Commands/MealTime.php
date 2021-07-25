<?php

namespace App\Console\Commands;

use App\Food;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MealTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meal:terminated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check the time and terminated meals are finished';

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
        \Log::info('time checked and terminated meals are finished');
        $meals = Food::all();
        if($meals->count() > 0)
        {
            foreach ($meals as $meal)
            {
                if ($meal->end_time < Carbon::now('Asia/Riyadh'))
                {
                    $meal->update([
                        'status'  => '1'
                    ]);
                }
                if ($meal->available == '0')
                {
                    $meal->update([
                        'status'  => '1'
                    ]);
                }
            }
        }
    }
}
