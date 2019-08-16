<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 10/24/2018
 * Time: 4:09 PM
 */

namespace Rsm\ElevateRecoveries;

interface ElevateRecoveriesInterface
{
    /**
     * This function is essentially going to "right shift up group" the raw data
     * based on a key a private function will define
     */
    public function elevate();
}