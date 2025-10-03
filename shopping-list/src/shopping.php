<?php declare(strict_types=1);

namespace CVUT\PHP\HW;

function getPrice(string $item): float
{
    $pattern1 = '/CZK\s*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)/';
    $pattern2 = '/([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*(?:CZK|Kč|,-)/';

    $matches = [];

    if (preg_match($pattern1, $item, $matches) || preg_match($pattern2, $item, $matches)) {
        $price = str_replace(['.', ','], ['', '.'], $matches[1]);
        return (float) $price;
    }

    return 0.0;
}

/**
 * @param string[] $list
 * @return string[]
 */
function sortList(array $list): array
{
    usort($list, function ($a, $b) {
        return getPrice($a) <=> getPrice($b);
    });
    return $list;
}

/**
 * @param string[] $list
 */
function sumList(array $list): float
{
    return array_reduce($list, function ($carry, $item) {
        return $carry + getPrice($item);
    }, 0.0);
}

// this disables the CLI interface when PHPUnit, the automated testing framework, runs
// do not remove, otherwise automated testing will fail. Thanks!
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    if (count($argv) !== 2) {
        echo "Usage: php shopping.php <input>" . PHP_EOL;
        exit(1);
    }
    $input = trim(file_get_contents(end($argv)));
    $list = explode(PHP_EOL, $input);
    $list = sortList($list);
    print_r($list);
    print_r(sumList($list) . PHP_EOL);
}