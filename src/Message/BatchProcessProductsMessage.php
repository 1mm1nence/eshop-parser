<?php

namespace App\Message;

use App\DTO\ProductDTO;

class BatchProcessProductsMessage
{
    /**
     * @param ProductDTO[] $products
     * @param string $csvPath
     */
    public function __construct(
        public array $products,
        public string $csvPath
    ) {}
}