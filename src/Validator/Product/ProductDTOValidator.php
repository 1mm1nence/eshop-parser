<?php

namespace App\Validator\Product;

use App\DTO\ProductDTO;
use App\Exception\Product\InvalidPriceException;
use App\Exception\Product\InvalidProductNameException;
use App\Exception\Product\InvalidUrlException;
use App\Validator\Product\Interface\ProductValidatorInterface;

class ProductDTOValidator implements ProductValidatorInterface
{
    public function validate(ProductDto $dto): void
    {
        if (mb_strlen(trim($dto->name)) < 2) {
            throw new InvalidProductNameException($dto->name);
        }

        if ($dto->price < 0 || $dto->price > 999999999999.99) {
            throw new InvalidPriceException($dto->price);
        }

        if (!$this->isValidHttpUrl($dto->productUrl)) {
            throw new InvalidUrlException('product', $dto->productUrl);
        }

        if (!$this->isValidHttpUrl($dto->imageUrl)) {
            throw new InvalidUrlException('image', $dto->imageUrl);
        }
    }

    private function isValidHttpUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) &&
            in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
