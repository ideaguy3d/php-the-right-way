<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 9/25/2018
 * Time: 2:49 AM
 */

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

class ReceiptTest extends TestCase
{
    private $Formatter;
    private $Receipt;
    
    function setUp() {
        $this->Formatter = $this->getMockBuilder('TDD\Receipt')
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
    
    function testSubTotalSumsCorrectlyWithTheGivenDataSet() {
    
    }
}