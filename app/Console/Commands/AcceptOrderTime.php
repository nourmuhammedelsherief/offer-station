<?php

namespace App\Console\Commands;

use App\Order;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AcceptOrderTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accept_order:minute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accept Order Time Determined From Dashboard';

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
        \Log::info('Accept Order Time Determined From Dashboard');
        $accept_time = Setting::find(1)->order_accept_time;
        $orders = Order::where('status' , '0')->get();
        if($orders->count() > 0)
        {
           foreach ($orders as $order)
           {
               if ($order->offers->count() > 0)
               {

               }else{
                   if ($order->created_at->addMinutes($accept_time) < Carbon::now())
                   {
                       $order->update([
                           'status'  => '3',
                       ]);
                   }
               }
           }
        }
    }
}
