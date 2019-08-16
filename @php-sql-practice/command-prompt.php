<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/29/2018
 * Time: 12:26 AM
 *
 * pass in vars from Command Prompt
 *
 */

//-- in command prompt:
// php cp.php "zip=95820&name=julius"
//if (!isset($_SERVER["HTTP_HOST"])) {
////    parse_str($argv[1], $_GET);
////    $getZip = $_GET['zip'];
////    $getName = $_GET['name'];
////    echo "\n\n getZip = $getZip and getName = $getName \n\n";
////}

// convert "V12345" to "sv12345"
$sv = str_replace("v", "sv", strtolower("V12345"));

echo "sv = $sv";




// end of php file