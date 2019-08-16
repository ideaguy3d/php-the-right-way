<?php

/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 8/24/2018
 * Time: 4:09 PM
 */

namespace TDD\Test;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

class ReceiptTest extends TestCase
{
    private $Receipt;
    private $Formatter;
    
    public function setUp() {
        $this->Formatter = $this->getMockBuilder('TDD\Formatter')
                                ->setMethods(['currencyAmount'])
                                ->getMock();
        
        $this->Formatter->expects($this->any())
                        ->method('currencyAmount')
                        ->with($this->anything())
                        ->will($this->returnArgument(0));
        
        $this->Receipt = new Receipt($this->Formatter);
    }
    
    public function tearDown() {
        unset($this->Receipt);
    }
    
    /**
     * @dataProvider provideSubtotal
     *
     * @param array $items - 1st idx in dp arr
     * @param int   $expected
     */
    public function testSubtotal($items, $expected) {
        $coupon = null;
        $output = $this->Receipt->subtotal($items, $coupon);
        $this->assertEquals(
            $expected,
            $output,
            "When summing the total with out coupon, should equal {$expected}"
        );
    }
    
    // data provider
    public function provideSubtotal() {
        return [
            "ints totaling 16" => [
                [1, 2, 5, 8], 16,
            ],
            "negative int" => [
                [-1, 2, 5, 8], 14,
            ],
            "3 ints totaling 11" => [
                [1, 2, 8], 11,
            ],
        ];
    }
    
    public function testSubtotalAndCoupon() {
        $input = [0, 2, 5, 8];
        $coupon = 0.20;
        $output = $this->Receipt->subtotal($input, $coupon);
        $this->assertEquals(
            12,
            $output,
            "When summing the total with coupon, should equal 12"
        );
    }
    
    public function testSubtotalException() {
        $input = [0, 2, 5, 8];
        $coupon = 1.20;
        $this->expectException('BadMethodCallException');
        $this->Receipt->subtotal($input, $coupon);
    }
    
    //-- Build a mock test double:
    public function testPostTaxTotal() {
        $items = [1, 2, 5, 8];
        $this->Receipt->tax = 0.20;
        $coupon = null;
        // Setup the Mock
        $Receipt = $this->getMockBuilder('TDD\Receipt')
                        ->setMethods(['tax', 'subtotal'])
                        ->setConstructorArgs([$this->Formatter])
                        ->getMock();
        // Invoke the subtotal method
        $Receipt->expects($this->once())
                ->method('subtotal')
                ->with($items, $coupon)
                ->will($this->returnValue(16.00));
        // Invoke the tax method
        $Receipt->expects($this->once())
                ->method('tax')
                ->with(16.00)
                ->will($this->returnValue(3.20));
        
        // This method invokes the prior 2 methods in the actual class
        $result = $this->Receipt->postTaxTotal([1, 2, 5, 8], null);
        $this->assertEquals(19.20, $result);
    }
    
    public function testTax() {
        $amount = 10;
        $this->Receipt->tax = 0.2;
        $output = $this->Receipt->tax($amount);
        $this->assertEquals(
            2,
            $output,
            'should equal 12 when testing tax'
        );
    }
}

