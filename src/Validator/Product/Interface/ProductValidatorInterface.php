<?php

namespace App\Validator\Product\Interface;

use App\Dto\ProductDto;
use App\Exception\Product\ProductValidationException;

interface ProductValidatorInterface
{
    /**
     * @throws ProductValidationException
     */
    public function validate(ProductDto $dto): void;
}