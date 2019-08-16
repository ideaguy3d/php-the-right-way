<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 8/19/2018
 * Time: 1:20 PM
 *
 */

// ep = end point
$ep = "localhost/ninja/app/commission/test/com-auto/start?precision=exact";
// $data = json_decode(file_get_contents($ep));

function testRegex1() {
    $re = '/(10.*?#|#.*?10)+/m';
    $str = 'some 10 # _ # white envelope
            another #10 envelope
            third # space 10 envelope
            simple 10# envelope';
    
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

    // Print the entire match result
    var_dump($matches);
    
    $break = 'point';
}

testRegex1();

$myHost = gethostname();
$break = 'point';

$newLines = "\n\r\n\r";
$info = "The accounting and coordinator data are different sizes ~routes.php line 333 ish";
$info = substr_replace($info, $newLines, 0, 0);
$info = substr_replace($info, $newLines, strlen($info), 0);

$break = 'point';

// test for correctly adding additional insert cost
$pricePerClick = 0.06;
$numInserts = 4;
$additionalInsertCost = 0.005;
if($numInserts > 1) {
    for($i = 1; $i < $numInserts; $i++) {
        $pricePerClick += $additionalInsertCost;
    }
}

echo "\n\n__>> price per click with $numInserts inserts = $pricePerClick\n\n";

// test parsing for stand envelopes from a raw string
$envelopePaperArr = [
    '#10 White', '#10 Brown Kraft', 'Brown Kraft', ' White #10 ', 'Monster White #10 ',
];
foreach($envelopePaperArr as $key => $value) {
    $result = strpos(strtolower($value), '#10');
    
    $break = 'point';
}


$mockData = [['company_name', 'total'], ['Spicers LLC', '3800']];
$mockDataJson = json_encode($mockData);

var_export($mockDataJson);

$break = 'point';

$userinfo['username'] = "ninjaHacker";
$firstLetter = $userinfo['username'][0];

$a = 210;
$b = 'a';

$sale = 200;
$sale = $sale - +1; // 199
$a = 20 % -8; // 4

$addr = "1010 Nörth Mäintåin ave";
$addr = strtr($addr, "äåö", "aao");

$a = 'somevalue';
$varArr = ["a2" => "One", "b2" => "Two", "c2" => "Three"];
extract($varArr);
echo "\$a = $a2; \$b = $b2; \$c = $c2";

$my_array = [1 => 'a', 2 => 'b'];

echo "\n\n";
echo $my_array;
echo "\n\n";


$letterUserName = strtoupper(str_split($userinfo['username'])[0]);
$letterUserName2 = strtoupper($userinfo['username'][0]);

echo "letter = $letterUserName";













// end of php file