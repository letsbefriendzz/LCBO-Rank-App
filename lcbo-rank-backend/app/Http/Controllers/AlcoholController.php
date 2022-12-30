<?php

namespace App\Http\Controllers;

use App\Http\Filters\AlcoholFilters;
use App\Http\Resources\AlcoholResource;
use App\Models\Alcohol;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlcoholController extends Controller
{
    public const PAGINATE_BY = 25;

    public function show(Alcohol $alcohol): AlcoholResource
    {
        return new AlcoholResource($alcohol);
    }

    public function index(AlcoholFilters $filters, Request $request)
    {
        // todo refactor where to model level?

        $alcohols = Alcohol::filter($filters)
            ->where('price_index', '!=', 'null')
            ->paginate(self::PAGINATE_BY);
        $alcohols->map(fn($a) => (new AlcoholResource($a))->toArray($request));
        return $alcohols;
//        return AlcoholResource::collection(
//        );
    }

    public function search(Request $request)
    {
        return AlcoholResource::collection(
            Alcohol::search($request->input('query', ''))
//                ->where('price_index', '!=', 'null') // todo: ask jacob!
                ->get()
        );
    }
}

