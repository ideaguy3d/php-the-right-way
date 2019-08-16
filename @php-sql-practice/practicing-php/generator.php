<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/7/2019
 * Time: 1:04 PM
 */
declare(strict_types=1);

// simple generator practice
function genPrac (int $init, int $max, int $incrementBy = 1) {
    for($i=$init; $i < $max; $i += $incrementBy) {
        yield $i;
    }
}

foreach (genPrac(1, 8, 2) as $item) {
    echo $item . ", ";
}






//