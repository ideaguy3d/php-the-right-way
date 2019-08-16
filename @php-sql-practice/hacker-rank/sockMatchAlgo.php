<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/2/2018
 * Time: 10:21 PM
 */

// Given an array of integers representing the color of each object
// Determine how many pairs of objects with matching colors there are.

$strArr = "6 5 2 3 5 2 2 1 1 5 1 3 3 3 5";

$arr1 = explode(" ", $strArr);
$arr1 = array_map(function ($rec) {
    return (int)$rec;
}, $arr1);
$testCase1 = [
    count($arr1),
    $arr1
];

$pairs = sockMerchant($testCase1[0], $testCase1[1]);

echo "\n There were $pairs matching pairs! ^_^ \n";

function sockMerchant($n, $ar) {
    $arCopy = array_slice($ar, 0);
    $totalMatches = 0;
    for ($i = 0; $i < $n; $i++) {
        $item = $arCopy[$i];
        $pos = $i + 1;
        $tempArray = array_slice($arCopy, $pos);
        if ($k = array_search($item, $tempArray)) {
            $matches = array_keys($ar, $tempArray[$k]);
            array_splice($ar, $matches[1], 1);
            ++$totalMatches;
        }
    }
    return $totalMatches;
}

// echo phpinfo();