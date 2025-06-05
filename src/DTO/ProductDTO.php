<?php

namespace App\DTO;

class ProductDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public string $imageUrl,
        public string $productUrl,
    ) {}
}