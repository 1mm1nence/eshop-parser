<?php

namespace App\Exception\Product;

class InvalidUrlException extends ProductValidationException
{
    public function __construct(string $field, string $url)
    {
        parent::__construct("Invalid $field URL: \"$url\". Must be a valid HTTP/HTTPS URL.");
    }
}