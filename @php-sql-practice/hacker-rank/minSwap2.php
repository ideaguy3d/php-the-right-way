<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 10/14/2018
 * Time: 11:18 PM
 */

// output number of swaps to sort arr

$input1 = [10, 30, 50, 20, 40, 60, 80, 9];

minimumSwap($input1);


/*
 1 - get arr in asc order
 2 - figure out how to get arr in asc order by swapping 2 values
 3 - figure out how to get the LEAST amount of swaps
    ... how many swapping combinations are there ?
 4 - return int that had the least swaps
*/
function minimumSwap (array $a): int {
    // get reference to how the sorted array should look
    $aCopy = $a;
    $swapCount = 0;
    $leastNumberOfSwaps = -1;

    sort($aCopy);

    for ($i = 0, $sortedIdx = 0; $i < count($a); $i++) {
        $rec = $a[$i];
        $sortedRefRec = $aCopy[$sortedIdx];
        // check if cur $rec is already smallest int in arr
        if($rec === $sortedRefRec) {
            $sortedIdx++;
            continue;
        }

        // INNER LOOP :(
        for($i2 = $i + 1; $i2 < (count($a) - $i); $i2++) {
            $rec2 = $a[$i2];
            if($rec2 < $rec) {
                break;
            }
        }
    }

    return $leastNumberOfSwaps;
}







//