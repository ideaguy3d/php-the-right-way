<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 3:52 PM
 */

namespace TDD;


class Formatter
{
    public function currencyAmount($input) {
        // do additional processing then round
        return round($input, 2);
    }
}