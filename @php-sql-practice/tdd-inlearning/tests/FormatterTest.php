<?php

/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 2:08 PM
 */

namespace TDD\Test;
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Formatter;

class FormatterTest extends TestCase
{
    protected $Formatter;
    
    public function setUp() {
        $this->Formatter = new Formatter();
    }
    
    public function tearDown() {
        unset($this->Formatter);
    }
    
    /**
     * @dataProvider provideCurrencyAmount_DataProvider
     *
     * @param mixed  $input - fromDataProvider, will almost always be an int or float
     * @param float  $expected - fromDataProvider, 2nd index
     * @param string $msg - fromDataProvider, info of test input
     *
     * @return void
     */
    public function testCurrencyAmount($input, $expected, $msg) {
        $this->assertSame(
            $expected,
            $this->Formatter->currencyAmount($input),
            $msg
        );
    }
    
    public function provideCurrencyAmount_DataProvider() {
        return [
            // 3 params
            [1, 1.00, '1 should be transformed to 1.00'],
            // 3 params
            [1.1, 1.10, '1.1 should be transformed to 1.10'],
            // 3 params
            [1.11, 1.11, '1.11 should stay as 1.11'],
            // 3 params
            [1.111, 1.11, '1.111 should become 1.11'],
        ];
    }
}