<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Illuminate\Support\Facades\Log;
use RoachPHP\Extensions\LoggerExtension;
use Symfony\Component\DomCrawler\Crawler;
use App\Spiders\Pipeline\ProductProcessor;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;

class SugarGangSpider extends BasicSpider
{
    public array $startUrls = [
        'https://sugargang.com/collections/alle-produkte'
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        ProductProcessor::class
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {

        foreach ($this->parseListPage($response) as $request) {
            yield $request;
        }

        // queue in next
        $nextPageLink = $response->filter('.pagination__next.link');
        if($nextPageLink->count() > 0) {
            yield $this->request('GET', $nextPageLink->link()->getUri(), 'parse');
        }


    }

    public function parseListPage(Response $response): Generator
    {
        $productItems = $response->filter('.product-list .product-item');
        $productsUri = [];

        $productsUri = $productItems->each(function (Crawler $productItem, $i) {
            $itemLink = $productItem->filter('.product-item__title.link');
            if($itemLink->count() > 0) {
                return $itemLink->link()->getUri();
            }
        });

        foreach($productsUri as $uri) {
            yield $this->request('GET', $uri, 'parseProductPage');
        }
    }

    public function parseProductPage(Response $response): Generator
    {
        $title = $response->filterXPath('//meta[contains(@property, "og:title")]')->attr('content');
        $price = $response->filterXPath('//meta[contains(@property, "product:price:amount")]')->attr('content');
        $image = $response->filterXPath('//meta[contains(@property, "og:image")]')->attr('content');

        $sku = $response->filterXPath('//*/@data-sku')->text();
        $ingredentPath = $response->filterXPath('//span[contains(text(), "Inhaltsstoffe")]/../../div/div/div');
        $ingredients = null;
        if($ingredentPath->count() > 0) {
            $ingredients = $ingredentPath->text();
        }

        yield $this->item(compact('title', 'price', 'image', 'sku', 'ingredients'));
    }
}
