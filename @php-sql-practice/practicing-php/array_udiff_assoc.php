<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/6/2019
 * Time: 7:24 PM
 */

class cr
{
    private $member;

    function __construct($val) {
        $this->member = $val;
    }

    public static function comp_func_cr($a, $b): int {
        return $a <=> $b;
    }

    public static function comp_func_cr2($a, $b): int {
        if ($a->member === $b->member) {
            return 0;
        }

        return ($a->member > $b->member) ? 1 : -1;
    }
}

class crDataOp
{
    private $member;

    function __construct($val) {
        $this->member = $val;
    }

    public static function comp_func_cr($a, $b): int {
        if ($a->member === $b->member) {
            return 0;
        }

        return ($a->member > $b->member) ? 1 : -1;
    }
}

$a = array(
    "0.1" => new cr(9),
    "0.5" => new cr(12),
    0 => new cr(23),
    1 => new cr(4),
    2 => new cr(-15)
);
$b = array(
    "0.2" => new cr(9),
    "0.5" => new cr(22),
    0 => new cr(3),
    1 => new cr(4),
    2 => new cr(-15)
);
$result = array_udiff_assoc($a, $b, array("cr", "comp_func_cr"));
print_r($result);

$aSet = ['0.1' => new crDataOp(9), '0.5' => null, ''];
$bSet = [];