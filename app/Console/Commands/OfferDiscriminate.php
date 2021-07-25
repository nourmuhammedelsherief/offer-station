<?php

namespace App\Console\Commands;

use App\Offer;
use App\UserOffer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OfferDiscriminate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:discriminate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'time checked and terminated Offer discriminate are finished';

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
        \Log::info('time checked and terminated Offer discriminate are finished');
        $offers = Offer::whereStatus('0')
            ->where('discriminate' , '1')
            ->get();
        if ($offers->count() > 0)
        {
            foreach ($offers as $offer)
            {

                if ($offer->remaining_views == 0 || $offer->views == $offer->views_count)
                {
                    $offer->update([
                        'discriminate'   => '0',
                        'views_count'    => 0,
                        'views_price'    => 0,
                    ]);
                }
            }
        }
    }
}
