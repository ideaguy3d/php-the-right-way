<?php

namespace TDD;

use \BadMethodCallException;

class Receipt
{
    public $tax = 0.10;
    public $Formatter;
    
    public function __construct(Formatter $formatter) {
        $this->Formatter = $formatter;
    }
    
    /**
     * @param array $items
     * @param float $coupon
     * @return float|int
     */
    public function subtotal(array $items, $coupon) {
        if($coupon > 1.00) {
            throw new BadMethodCallException('Coupon must be < 1.00');
        }
        
        $sum = array_sum($items);
        
        if (!is_null($coupon)) {
            return $sum - ($sum * $coupon);
        }
        
        return $sum;
    }
    
    /**
     * @param float $amount
     * @return float|int
     */
    public function tax($amount) {
        return $this->Formatter->currencyAmount($amount * $this->tax);
    }

    /**
     * @param array $items
     * @param float $coupon
     * @return float|int
     */
    public function postTaxTotal($items, $coupon) {
        $subtotal = $this->subtotal($items, $coupon);
        return $subtotal + $this->tax($subtotal);
    }
}

//$r = new Receipt();
//echo "\r\npostTaxTotal =\n\r";
//echo $r->postTaxTotal([1, 2, 5, 8], 0.20, null);
//echo "\n\rarray_sum() = " . array_sum([1, 2, 5, 8]);