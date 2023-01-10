<?php

namespace Tests\Feature;

use App\Events\PricesUpdated;
use App\Models\Alcohol;
use Tests\TestCase;

class CheckForPriceChangesTest extends TestCase
{
    public function test_it_finds_init_price_changes()
    {
        $alcoholId = 1;
        $initPrice = 6.90;
        Alcohol::query()->upsert(Alcohol::factory()->raw([
            'permanent_id' => $alcoholId,
            'price' => $initPrice,
        ]), ['permanent_id']);

        PricesUpdated::dispatch();

        $this->assertDatabaseHas('price_changes', [
            'permanent_id' => $alcoholId,
            'price' => $initPrice,
        ]);
    }

    public function test_it_finds_price_changes()
    {
        $alcoholId = 1;
        $initPrice = 6.90;
        $updatePrice = 3.50;

        Alcohol::query()->upsert(Alcohol::factory()->raw([ // first upsert simulates initial creation of a record
            'permanent_id' => $alcoholId,
            'price' => $initPrice,
        ]), ['permanent_id']);

        PricesUpdated::dispatch();

        Alcohol::query()->upsert(Alcohol::factory()->raw([ // second upset simulates updating of an existing record
            'permanent_id' => $alcoholId,
            'price' => $updatePrice,
        ]), ['permanent_id']);

        PricesUpdated::dispatch();

        $this->assertDatabaseCount('price_changes', 2); // command should have logged the init and update
        $this->assertDatabaseHas('price_changes', [
            'permanent_id' => $alcoholId,
            'price' => $initPrice, // todo jacob pls justify this bigint awfulness
        ]);
        $this->assertDatabaseHas('price_changes', [
            'permanent_id' => $alcoholId,
            'price' => $updatePrice,
        ]);
    }

    public function test_it_doesnt_log_observer_init_price_changes()
    {
        $permanentId = 1;
        Alcohol::factory()->create([
            'permanent_id' => $permanentId,
            'price' => 3.50,
        ]);

        $this->assertDatabaseCount('price_changes', 1);
        $this->assertDatabaseHas('price_changes', [
            'permanent_id' => $permanentId,
        ]);

        PricesUpdated::dispatch();

        $this->assertDatabaseCount('price_changes', 1);
        $this->assertDatabaseHas('price_changes', [
            'permanent_id' => $permanentId,
        ]);
    }

    public function test_it_doesnt_trample_its_own_init() // todo  what?
    {
        $alcoholId = 1;
        $initPrice = 6.90;

        Alcohol::query()->upsert(Alcohol::factory()->raw([ // first upsert simulates initial creation of a record
            'permanent_id' => $alcoholId,
            'price' => $initPrice,
        ]), ['permanent_id']);

        PricesUpdated::dispatch();

        $this->assertDatabaseCount('price_changes', 1);

        PricesUpdated::dispatch();

        $this->assertDatabaseCount('price_changes', 1);

        PricesUpdated::dispatch();

        $this->assertDatabaseCount('price_changes', 1);
    }

    public function test_it_detects_if_price_hasnt_changed()
    {
        Alcohol::factory()->create([
            'price' => 3.95,
        ]);

        PricesUpdated::dispatch();
        $this->assertDatabaseCount('price_changes', 1);
        PricesUpdated::dispatch();
        $this->assertDatabaseCount('price_changes', 1);
        PricesUpdated::dispatch();
        $this->assertDatabaseCount('price_changes', 1);
    }
}
