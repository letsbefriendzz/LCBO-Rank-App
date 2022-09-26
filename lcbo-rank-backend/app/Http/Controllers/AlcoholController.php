<?php

namespace App\Http\Controllers;

use App\Models\Alcohol;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AlcoholController extends Controller
{
    public const DEFAULT_ALCOHOLS_RETURNED = 25;

    public function show(Alcohol $alcohol): Alcohol
    {
        return $alcohol;
    }

    public function getEfficient(
        Request $request,
        string  $category = '',
        string  $subcategory = ''
    ): Collection
    {
        $maxIndex = $request->input('maxPriceIndex', 1000);
        $minIndex = $request->input('minPriceIndex', 0);
        $sortAscendingDescending = $request->input('order', 'asc');

        $numberOfResults = $request->input('numberOfResults', AlcoholController::DEFAULT_ALCOHOLS_RETURNED);

        $query = DB::table('alcohols')->orderBy('price_index');

        if ($category)
            $query->where('category', '=', $category);

        if ($subcategory)
            $query->where('subcategory', '=', $subcategory);

        return $query
            ->where('price_index', '>', $minIndex)
            ->where('price_index', '<', $maxIndex)
            ->orderBy('price_index', $sortAscendingDescending)
            ->get()
            ->take($numberOfResults);
    }

    public function getDefault(Request $request): Collection
    {
        $id = $request->input('id', '');
        $title = $request->input('title', '');
        $brand = $request->input('brand', '');
        $category = $request->input('category', '');
        $subcategory = $request->input('subcategory', '');
        $country = $request->input('country', '');
        $outOfStock = $request->input('outOfStock', '');

        // min & max values
        $maxIndex = $request->input('maxPriceIndex', 1000);
        $minIndex = $request->input('minPriceIndex', 0);
        $maxPrice = $request->input('maxPrice', 100000);
        $minPrice = $request->input('minPrice', 0);
        $maxVolume = $request->input('maxVolume', 100000);
        $minVolume = $request->input('minVolume', 0);
        $maxAlcoholContent = $request->input('maxAlcoholContent', 100000);
        $minAlcoholContent = $request->input('minAlcoholContent', 0);

        $sortCondition = $request->input('sortBy', '');
        $sortAscendingDescending = $request->input('order', 'asc');

        $numberOfResults = $request->input(
            'numberOfResults',
            AlcoholController::DEFAULT_ALCOHOLS_RETURNED
        );

        $query = DB::table('alcohols');

        if ($sortAscendingDescending != 'asc' && $sortAscendingDescending != 'desc')
            $sortAscendingDescending = 'desc';

        if ($id)
            $query->where('id', '=', $id);

        if ($title)
            $query->where('title', '=', $title);

        if ($brand)
            $query->where('brand', '=', $brand);

        if ($category)
            $query->where('category', '=', $category);

        if ($subcategory)
            $query->where('subcategory', '=', $subcategory);

        if ($country)
            $query->where('country', '=', $country);

        if ($outOfStock)
            $query->where('out_of_stock', '=', $outOfStock);

        if ($sortCondition)
            $query->orderBy($sortCondition, $sortAscendingDescending);

        // todo introduce conditions on these?

        // INDEX
        $query->where('price_index', '<=', $maxIndex);
        $query->where('price_index', '>=', $minIndex);

        // PRICE
        $query->where('price', '<=', $maxPrice);
        $query->where('price', '>=', $minPrice);

        // VOLUME
        $query->where('volume', '<=', $maxVolume);
        $query->where('volume', '>=', $minVolume);

        // ALCOHOL CONTENT
        $query->where('alcohol_content', '<=', $maxAlcoholContent);
        $query->where('alcohol_content', '>=', $minAlcoholContent);

        return $query
            ->get()
            ->take($numberOfResults);
    }

    // todo test this
    public function updatedStatus(): JsonResponse
    {
        $oneDayAgo = Carbon::today()->subDay();
        $updatedRecords = Alcohol::query()->where('updated_at', '<', $oneDayAgo)->get();

        return response()->json([
            'recordsUpdated' => $updatedRecords->toJson(),
        ]);
    }
}

