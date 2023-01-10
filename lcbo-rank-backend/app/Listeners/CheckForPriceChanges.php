<?php

namespace App\Listeners;

use App\Models\Alcohol;
use App\Models\PriceChange;

class CheckForPriceChanges
{
    public function handle(): void
    {
        // if the alcohol price is different from the most recent price change, create new record
        Alcohol::all()->each(function (Alcohol $alcohol) {
            if ($alcohol->newest_price_change != $alcohol->price) {
                PriceChange::create([
                    'permanent_id' => $alcohol->permanent_id,
                    'price' => $alcohol->price,
                ]);
            }
        });
    }
}
