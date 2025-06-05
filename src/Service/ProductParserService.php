<?php

namespace App\Service;

use App\DTO\ProductDTO;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ProductParserService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @return ProductDTO[]
     */
    public function parseProducts(string $html): array
    {
        $productDTOs = [];

        $crawler = new Crawler($html);

        // Відокремив частину з продуктами, аби не шукало по всій сторінці.
        $productsNode = $crawler->filterXPath('//rz-category-goods');
        $crawler = new Crawler($productsNode->html());

        $crawler->filterXPath('//rz-product-tile[contains(@class, "tile")]')->each(function (Crawler $node) use (&$productDTOs) {
            try {
                $title = $node->filterXPath('.//a[contains(@class, "tile-title")]')->text();
                $imageUrl = $node->filterXPath('.//img[contains(@class, "tile-image")]')->attr('src') ?? '';
                $productUrl = $node->filterXPath('.//a[contains(@class, "tile-image-host")]')->attr('href') ?? '';

                $rowPriceText = $node->filterXPath('.//div[contains(concat(" ", normalize-space(@class), " "), " price ")]')->text();
                $price = $this->parseProductPrice($rowPriceText);

                $productDTOs[] = new ProductDTO($title, $price, $imageUrl, $productUrl);
            } catch (InvalidArgumentException $e) {
                $this->logger->error('While parsing, skipped product due to invalid argument passed to product DTO: ' . $e->getMessage());
            }
        });

        return $productDTOs;
    }

    private function parseProductPrice(string $rowPriceText): float
    {
        $priceString = str_replace(['₴', "\u{00A0}", ' '], '', $rowPriceText);
        $priceString = str_replace(',', '.', $priceString);

        $price = (float) preg_replace('/[^\d.]/', '', $priceString);

        return $price;
    }
}
