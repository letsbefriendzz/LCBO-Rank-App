<?php

namespace Tests\Feature;

use App\Models\Alcohol;
use Tests\TestCase;

class AlcoholTest extends TestCase
{
    public function test_it_can_get_its_newest_price_change()
    {
        $alc = Alcohol::factory()->create([
            'price' => 3.95,
        ]);
        $alc->update(['price' => 4.45]);
        $alc->update(['price' => 3.95]);

        dump($alc->priceChanges->toArray());

        $this->assertEquals(3.95, $alc->newest_price_change);
    }

    public function test_it_can_get_its_oldest_price()
    {
        $alc = Alcohol::factory()->create([
            'price' => 3.50,
        ]);
        $alc->update(['price' => 4.45]);
        $alc->update(['price' => 3.95]);
        $alc->update(['price' => 4.45]);

        dump($alc->priceChanges->toArray());

        $this->assertEquals(3.50, $alc->oldest_known_price);
    }

    public function test_it_can_get_its_highest_price()
    {
        $highestPrice = 10000;
        $alc = $this->createAlcoholWithPriceChanges();
        $alc->update(['price' => $highestPrice]);

        $this->assertEquals($highestPrice, $alc->highest_price);
    }

    public function test_it_can_get_its_lowest_price()
    {
        $lowestPrice = 0.50;
        $alc = $this->createAlcoholWithPriceChanges();
        $alc->update(['price' => $lowestPrice]);

        $this->assertEquals($lowestPrice, $alc->lowest_price);
    }

    public function createAlcoholWithPriceChanges(float $initPrice = 10.00, float $latestPrice = 12.0)
    {
        $alc = Alcohol::factory()->create([
            'price' => $initPrice,
        ]);
        $alc->update(['price' => 13.50]);
        $alc->update(['price' => 12.45]);
        $alc->update(['price' => 14.25]);
        $alc->update(['price' => $latestPrice]);
        return $alc;
    }
}
