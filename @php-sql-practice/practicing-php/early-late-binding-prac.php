<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/20/2019
 * Time: 10:40 PM
 */


$s = "some string";

// early binding
$early = function() use ($s) {
    echo $s;
};
$s = "\nearly binding\n";
$early();

// late binding
$late = function() use (&$s) {
    echo $s;
};
$s = "\nlate binding\n";
$late();






//