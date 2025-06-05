<?php

namespace App\Exception\Product;

class InvalidPriceException extends ProductValidationException
{
    public function __construct(float $price)
    {
        parent::__construct("Invalid price: $price. Must not be negative and less then 999999999999.99.");
    }
}