<?php

namespace App\Console\Commands;

use App\Models\Alcohol;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use stdClass;

class UpdateAlcoholData extends Command
{
    private const GET_IN_EACH_REQUEST = 250;
    private const AUTH_TOKEN = 'Bearer xx883b5583-07fb-416b-874b-77cce565d927';
    public const SEARCH_REQ_URL = 'https://platform.cloud.coveo.com/rest/search/v2?organizationId=lcboproductionx2kwygnc';
    public const COPIED_HEADERS = [
        "accept" => "*/*",
        "accept-language" => "en-US,en;q=0.9",
        "authorization" => self::AUTH_TOKEN,
        "content-type" => "application/x-www-form-urlencoded; charset=UTF-8",
        "sec-ch-ua" => "\".Not/A)Brand\";v=\"99\", \"Google Chrome\";v=\"103\", \"Chromium\";v=\"103\"",
        "sec-ch-ua-mobile" => "?0",
        "sec-ch-ua-platform" => "\"Windows\"",
        "sec-fetch-dest" => "empty",
        "sec-fetch-mode" => "cors",
        "sec-fetch-site" => "cross-site",
        "referrer" => "https://www.lcbo.com/",
        "referrerPolicy" => "strict-origin-when-cross-origin",
        "mode" => "cors",
        "credentials" => "include",
    ];

    protected $signature = 'alcohol:update {--category=Products}';
    protected $description = 'Updates the database with the latest information from the LCBO\'s API.';

    // todo fixture file

    /**
     * @throws GuzzleException
     */
    public function handle(Client $client): void // todo HTTPFacade
    {
        // todo read about dependency injection
        $category = $this->option('category');
        $initResponse = $client->request('POST', self::SEARCH_REQ_URL, [
            "headers" => self::COPIED_HEADERS,
            "form_params" => [
                "aq" => "@ec_category=${category}",
                "firstResult" => 0,
                "numberOfResults" => 0,
            ],
        ]);

        $totalCount = json_decode($initResponse->getBody()->getContents())->totalCount;
        dump($totalCount);
        $recordsScraped = 0;

        while ($recordsScraped < $totalCount) {
            $response = $client->request('POST', self::SEARCH_REQ_URL, [
                "headers" => self::COPIED_HEADERS,
                "form_params" => [
                    "aq" => "@ec_category==\"" . $this->option('category') . "\"",
                    "numberOfResults" => self::GET_IN_EACH_REQUEST,
                    "firstResult" => $recordsScraped,
                ],
            ]);
            $alcoholsReturned = collect(json_decode($response->getBody()->getContents())->results);
            $alcoholsReturned->each(function ($alcohol) {
                $alcohol = $alcohol->raw;

//                $foo = Alcohol::query()->find($alcohol->permanentid);
//
//                if ($foo) {
//                    // todo make updating a thing
//                    dump('badbadnotgood');
//                    return;
//                }

                Alcohol::query()->create($this->getProperties($alcohol));
            });

            if($recordsScraped == 2750)
                dd($alcoholsReturned->count());

            $recordsScraped += $alcoholsReturned->count();
            dump("Scraped: $recordsScraped / $totalCount");
            sleep(1);
        }
    }

    // headaches ! :)
    public function getProperties(stdClass $alcohol): array
    {
        $title = trim($alcohol->title);
        $brand = $alcohol->ec_brand ?? null;
        $category = isset($alcohol->ec_category_filter) ? explode("|", $alcohol->ec_category_filter[0])[1] : "";
        $subcategory = explode("|", $alcohol->ec_category_filter[0])[2];
        $price = $alcohol->ec_price ?? -1;
        $volume = $alcohol->lcbo_total_volume ?? $this->truncatedVolumeToInteger(isset($alcohol->lcbo_unit_volume) ?: 0);
        $alcohol_content = $alcohol->lcbo_alcohol_percent ?? 0.0;
        $price_index = 0.0;
        $country = $alcohol->country_of_manufacture ?? '';
        $url = $alcohol->sysuri;
        $thumbnail_url = $alcohol->ec_thumbnails;
        $image_url = str_replace('319.319', '1280.1280', $alcohol->ec_thumbnails);
        $out_of_stock = $alcohol->out_of_stock;
        $description = isset($alcohol->ec_shortdesc) ? trim($alcohol->ec_shortdesc) : '';
        $rating = $alcohol->ec_rating ?? 0.0;
        $reviews = $alcohol->avg_reviews ?? 0;
        $permanent_id = $alcohol->permanentid;

        return [
            'permanent_id' => $permanent_id,
            'title' => $title,
            'brand' => $brand,
            'category' => $category,
            'subcategory' => $subcategory,
            'price' => $price,
            'volume' => $volume,
            'alcohol_content' => $alcohol_content,
            'price_index' => $price_index,
            'country' => $country,
            'url' => $url,
            'thumbnail_url' => $thumbnail_url,
            'image_url' => $image_url,
            'out_of_stock' => $out_of_stock,
            'description' => $description,
            'rating' => $rating,
            'reviews' => $reviews,
        ];
    }

    /**
     * @param string $truncatedValue
     * @return int
     * todo make sure this works as intended lol
     */
    public static function truncatedVolumeToInteger(string $truncatedValue): int
    {
        $volumes = collect(explode('x', $truncatedValue));

        $totalVolume = 0;
        $volumes->each(function ($volume) use (&$totalVolume, $volumes) {
            if($volumes->first() == $volume) {
                $totalVolume = $volume;
            }
            else {
                $totalVolume *= $volume;
            }
        });

        return $totalVolume;
    }
}
