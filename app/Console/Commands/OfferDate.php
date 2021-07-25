<?php

namespace App\Console\Commands;

use App\Favorite;
use App\Offer;
use App\Report;
use App\UserOffer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OfferDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check offers time';

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
        \Log::info('time checked and terminated Offer are finished');
        $offers = Offer::whereStatus('0')
            ->get();
        if ($offers->count() > 0)
        {
            foreach ($offers as $offer)
            {
                $now = Carbon::now();
                if ($now > $offer->offer_time)
                {
                    $offer->update([
                        'status'   => '1',
                    ]);
                    Favorite::whereOfferId($offer->id)->delete();
                    Report::whereOfferId($offer->id)->delete();
                }
                if ($offer->end_date < Carbon::now())
                {
                    UserOffer::whereOfferId($offer->id)->delete();
                }
                $user_offers = UserOffer::whereOfferId($offer->id)->count();
                if ($user_offers >= $offer->max_quantity)
                {
                    $offer->update([
                        'status'   => '1',
                    ]);
                    UserOffer::whereOfferId($offer->id)->delete();
                }
            }
        }
    }
}
