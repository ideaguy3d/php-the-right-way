<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/14/2018
 * Time: 10:01 AM
 */

//--------------------
// generator practice
//--------------------
function myGenFunc($n1, $n2, $ctr = 1) {
    for ($i = $n1; $i <= $n2; $i += $ctr) {
        yield $i;
    }
}

function xrange($start, $limit, $step) {
    if ($start < $limit) {
        if ($step <= 0) {
            throw new LogicException("umm, step <= 0");
        }

        for ($i = $start; $i <= $limit; $i += $step) {
            yield $i;
        }
    }
}

function display() {
    for ($i = 0; $i < count([1,2,3,4]); $i++) {
        $rec = [1,2,3,4][$i]; 
        yield $rec;
    }
}

outputGeneratorResults();
function outputGeneratorResults() {
    echo "\n-----------------------";
    echo "\ngenerator practice";
    echo "\n-----------------------";

    $ret = display();
    foreach ($ret as $num) {
        echo "\n ret num = $num";
    }
    
    foreach (myGenFunc(1, 7, 2) as $num) {
        echo "\n num = $num \n";
    }

    echo "\n - xrange - \n";

    foreach (xrange(1, 9, 2) as $num) {
        echo " xrange num = $num ";
    }
}

function dereferencePrac() {
    $hello = "hello";
    $world = ['w', 'o', 'r', 'l', 'd'];
    echo "\n dereference prac \n";
    echo $hello[2]; // could also do "hello"[2]
    echo "\n";
    echo $world[2]; // could also do ['w', 'o', 'r', 'l', 'd'][2]
}

//-----------
// list() prac
//------------
// listPrac();
function listPrac() {
    $myMultiArray = [
        [1, 2, 7, 10],
        [22, 55, 7, 10],
        [256, 20, 7, 10],
        [789, 34, 7, 10],
    ];

    foreach ($myMultiArray as list($n1, $n4)) {
        echo "\n n1 = $n1, n2 = $n4 \n";
    }
}

