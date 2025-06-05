<?php

namespace App\Exception\Product;

class InvalidProductNameException extends ProductValidationException
{
    public function __construct(string $name)
    {
        parent::__construct("Invalid product name: \"$name\". Name must be at least 2 characters.");
    }
}