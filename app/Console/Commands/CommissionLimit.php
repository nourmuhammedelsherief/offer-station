<?php

namespace App\Console\Commands;

use App\ElectronicPocket;
use App\Setting;
use App\UserDevice;
use Illuminate\Console\Command;

class CommissionLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissionLimit:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'commission Check every day and if the commission Large than the limit make the user un active';

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
        \Log::info('commission Check every day and if the commission Large than the limit make the user un active');
        $commissionLimit = Setting::find(1)->commission_limit;
        $wallets = ElectronicPocket::where('cash' , '<=' , -$commissionLimit )->get();
        if ($wallets->count() > 0)
        {
            foreach ($wallets as $wallet)
            {
                $wallet->user->update([
                    'active' => 0
                ]);
                // send Notification to user
                $devicesTokens =  UserDevice::where('user_id',$wallet->user->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification(trans('العمولات'), trans('يجب عليك دفع  العموله  المستحقه قبل استخدام تطبيق أطعام') ,$devicesTokens);
                }
                saveNotification($wallet->user->id, trans('العمولات'), '3', trans('يجب عليك دفع  العموله  المستحقه قبل استخدام تطبيق أطعام'), null , null);
            }
        }
    }
}
