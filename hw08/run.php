<?php

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__.'/vendor/autoload.php';

function text(Crawler $crawler, string $selector)
{
    $new = $crawler->filter($selector);
    if (count($new)) {
        return trim($new->text());
    }

    return null;
}

/**
 * @param string $query - query string e.g. 'Beats Studio 3'
 * @return array
 */
function alzaScrape(string $query)
{
    $client = new Client();
    $client->setHeader('user-agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
    $results = [];
    $query = preg_replace("/ /",'%', trim($query));
    $url = "https://www.alza.cz/search.htm?exps=$query";
    $res = $client->request('GET', $url);
    $res->filter('#boxes .browsingitem .top .fb a')->each(function ($node) use ($client, &$results) {
        $elementUrl = "https://www.alza.cz" . $node->attr('href');
        $elementName = trim($node->text());
        $crawler = $client->request('GET', $elementUrl);
        $results[] = getElementInfoAlza($crawler, $elementName, $elementUrl);
    });

    return $results;
}

/**
 * @param Crawler $crawler
 * @return array
 */
function getElementInfoAlza(Crawler $crawler, $elementName, $elementUrl)
{
    $price = 0;
    $description = 0;

    $crawler->filter('#prices span.bigPrice')->each(function ($node) use (&$price) {
        $price = $node->text();
    });

    if($price == 0) {
        $crawler->filter('tr.pricenormal td.c2 span.price_withVat')->each(function ($node) use (&$price) {
            $price = $node->text();
        });
    }

    if($price == 0) {
        $crawler->filter('tr.pricenormal td.c2 span')->each(function ($node) use (&$price) {
            $price = $node->text();
        });
    }

    if($price == 0) {
        $crawler->filter('.mediaPriceDetail #prices tr.priceactionnormal td.c2 span')->each(function ($node) use (&$price) {
            $price = $node->text();
        });
    }

    $crawler->filter('#detailText div.nameextc span')->each(function($node) use (&$description) {
        $description = $node->text();
    });

    if($description == 0) {
        $crawler->filter('#detailText div.media-details div.row span')->each(function($node) use (&$description) {
            $description = $node->text();
        });
    }

    if($description == 0) {
        $crawler->filter('#mediaDetailText > div.nameextc > span')->each(function($node) use (&$description) {
            $description = $node->text();
        });
    }

    $element = [
        'name' => $elementName,
        'price' => trim($price),
        'link' => $elementUrl,
        'eshop' => 'Alza',
        'description' => trim($description)
    ];

    return $element;

}

function czcScrape(string $query)
{
    $client = new Client();
    $client->setHeader('user-agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
    $results = [];
    $query = preg_replace("/ /",'%20', trim($query));
    $url = "https://www.czc.cz/$query/hledat";
    $res = $client->request('GET', $url);
    $res->filter('#tiles > div > div > div.overflow > div.tile-title > h5 > a')->each(function ($node) use ($client, &$results) {
        $elementUrl = "https://www.czc.cz" . $node->attr('href');
        $elementName = trim($node->text());
        $crawler = $client->request('GET', $elementUrl);
        $results[] = getElementInfoCZC($crawler, $elementName, $elementUrl);
    });

    return $results;
}

function getElementInfoCZC(Crawler $crawler, $elementName, $elementUrl)
{
    $price = 0;
    $description = 0;

    $crawler->filter('#product-price-and-delivery-section > div.left > div.total-price > span > span.price-vatin')->each(function ($node) use (&$price) {
        $price = $node->text();
    });

    $crawler->filter('#product-detail > div.pd-wrap > div.pd-info > p')->each(function($node) use (&$description) {
        $description = trim($node->text());
    });

    $element = [
        'name' => $elementName,
        'price' => trim($price),
        'link' => $elementUrl,
        'eshop' => 'CZC',
        'description' => trim($description)
    ];

    return $element;
}

function scrape(string $query)
{
    $resultAlza = alzaScrape($query);
    $resultCZC = czcScrape($query);
    $result = array_merge($resultAlza, $resultCZC);
    usort($result, function ($a, $b) {
       return getPrice($a['price']) > getPrice($b['price']);
    });

    return $result;
}

function getPrice($item) {

    $item = htmlentities($item, null, 'utf-8');
    $item = str_replace("&nbsp;", "", $item);
    $item = html_entity_decode($item);

    $abovezero = 0;

    if( preg_match("/(\d)+/", $item, $tmp) > 0 ) {
        $abovezero =intval($tmp[0]);
    }

    return $abovezero;
}

/**
 * @param string $query   - query string e.g. 'Beats Studio 3'
 * @param array  $results - input product collection
 * @return array
 */
function filter(string $query, array $results)
{
    foreach ($results as $result) {
        $name = strtoupper($result['name']);
        $query = strtoupper($query);
        $queryArray = explode(' ', $query);
        $match = true;
        foreach ($queryArray as $word) {
            if(!preg_match("/.*$word.*/", $name)) {
                $match = false;
            }
        }
        if(!$match) {
            unset($results[array_search($result, $results)]);
        }
    }
    return $results;
}

/**
 * input array $results show contain following keys
 * - 'name'
 * - 'price'
 * - 'link' - link to product detail page
 * - 'eshop' - eshop identifier e.g. 'alza'
 * - 'description'
 *
 * @param array $results
 */
function printResults(array $results, bool $includeDescription = false)
{
    $width = [
        'name' => 0,
        'price' => 0,
        'link' => 0,
        'eshop' => 0,
        'description' => 0,
    ];

    foreach ($results as $result) {
        foreach ($result as $key => $value) {
            $width[$key] = max(mb_strlen($value), $width[$key]);
        }
    }

    echo '+'.str_repeat('-', 2 + $width['name']);
    echo '+'.str_repeat('-', 2 + $width['price']);
    echo '+'.str_repeat('-', 2 + $width['link']);
    echo '+'.str_repeat('-', 2 + $width['eshop'])."+\n";


    foreach ($results as $result) {

        echo '| '.$result['name'].str_repeat(' ', $width['name'] - mb_strlen($result['name'])).' ';
        echo '| '.$result['price'].str_repeat(' ', $width['price'] - mb_strlen($result['price'])).' ';
        echo '| '.$result['link'].str_repeat(' ', $width['link'] - mb_strlen($result['link'])).' ';
        echo '| '.$result['eshop'].str_repeat(' ', $width['eshop'] - mb_strlen($result['eshop'])).' ';
        echo "|\n";
        echo '+'.str_repeat('-', 2 + $width['name']);
        echo '+'.str_repeat('-', 2 + $width['price']);
        echo '+'.str_repeat('-', 2 + $width['link']);
        echo '+'.str_repeat('-', 2 + $width['eshop'])."+\n";
        if ($includeDescription) {
            echo '| '.$result['description'].str_repeat(' ',
                    max(0, 7 + $width['name'] + $width['price'] + $width['link'] - mb_strlen($result['description'])));
            echo "|\n";
            echo str_repeat('-', 10 + $width['name'] + $width['price'] + $width['link'])."\n";
        }
    }
}

// MAIN
if (count($argv) !== 2) {
    echo "Usage: php run.php <query>\n";
    exit(1);
}

$query = $argv[1];
$results = scrape($query);
$results = filter($query, $results);
printResults($results);
