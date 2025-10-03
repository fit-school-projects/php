<?php declare(strict_types=1);

namespace BIPHP\Application;

use BIPHP\Entity\ProductResult;
use BIPHP\Entity\RatingResult;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;


/**
 * This class represents your scrapping application
 */
class App
{
    /** @var ProductResult[] */
    private array $productResults = [];


    // Implement me please :)

    public function run(): void
    {
        $client = new HttpBrowser();

        $crawler = $client->request('GET', 'https://bi-php.urbanec.cz/products/search?form%5Bsearch%5D=phone');
        $crawler->filter('.container > .row > .col')->each(function (Crawler $prodList) use ($client) {
            $product = new ProductResult();
            $id = $prodList->filter('div[id^=product-card]')
                ->attr('id');
            preg_match('/product-card-(\d+)/', $id, $matches);
            $id = (int) $matches[1];

            $title = $prodList->filter('div > .card-body > h5')->text();
            $link = $prodList->filter('div > .card-body > a')->link()->getUri();
            $linkToCLick = $prodList->filter('div > .card-body > a')->link();
            $product->setId($id);
            $product->setName($title);
            $product->setLink($link);

            // redirect to product page
            $crawlerDesc = $client->click($linkToCLick);

            $description = $crawlerDesc->filter('.container > .card > .row > .col-md-8 > .card-body #desc')->text();
            $product->setDescription($description);

            $totalPrice = $this->getPriceFromString($crawlerDesc->filter('.container > .card > .row > .col-md-8 > .card-body > #total_price')->text());
            $totalPriceDiscount = null;
            if ($crawlerDesc->filter('.container > .card > .row > .col-md-8 > .card-body > #total_price_discount')->count() > 0) {
                $totalPriceDiscount = $this->getPriceFromString($crawlerDesc->filter('.container > .card > .row > .col-md-8 > .card-body > #total_price_discount')->text());
            }
            if ($totalPriceDiscount !== null) {
                $product->setTotalPrice($totalPriceDiscount);
                $product->setTotalPriceWithoutDiscount($totalPrice);
            } else {
                $product->setTotalPrice($totalPrice);
            }

            $crawlerDesc->filter('.container > .card')->last()->filter('.card-body > .card-body')->each(
                function (Crawler $rating) use ($product) {
                    $username = $rating->filter('.card-body > h5')->text();
                    $comment = $rating->filter('.card-body > .row > p')->text();
                    $rating = (int)$rating->filter('.card-body > .row > div > div > div')->text();
                    $product->addRating(new RatingResult($username, $comment, $rating));
                }
            );
            $this->productResults[] = $product;
        });

        // sort products by total price
        usort($this->productResults, function (ProductResult $a, ProductResult $b) {
            return $a->getTotalPrice() <=> $b->getTotalPrice();
        });

        // filter products that are more expensive than 25000 and products that are not phones
        $this->productResults = array_filter(
            $this->productResults,
            fn(ProductResult $p) =>
                $p->getTotalPrice() < 25000 &&
                $p->getName() !== 'Bag for Samsung (black)' &&
                $p->getName() !== 'Garmin Venu 2 Plus Slate/Black Band'
        );
        $this->productResults = array_values($this->productResults);
    }


    /**
     * Method which returns all products which we search. Products are ordered by total price.
     * @return ProductResult[]
     */
    public function getProductResults(): array
    {
        return $this->productResults;
    }

    /**
     * Method which returns averages for all products we have found form web.
     * @return array<int, float> - it is an array of averages. Product id is the key.
     * @example ['1' => 55.4, '2' => 67.8, '3' => 79,3 ... ]
     */
    public function getProductsRatingAvg(): array
    {
        $productsRatingAvg = [];
        foreach ($this->productResults as $product) {
            $ratings = $product->getRatings();
            $sum = 0;
            foreach ($ratings as $rating) {
                $sum += $rating->getRating();
            }
            $avg = $sum / count($ratings);
            $productsRatingAvg[$product->getId()] = $avg;
        }
        return $productsRatingAvg;
    }


    public function getPriceFromString(string $price): float
    {
        preg_match('/\d+(\.\d{1,2})?/', $price, $matches);
        $priceNum = $matches[0];
        return (float) $priceNum;
    }
}
