<?php

namespace App\Service;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Utility\CsvWriter;
use Doctrine\ORM\EntityManagerInterface;

class ProductProcessorService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CsvWriter $csvWriter
    ) {
    }

    /**
     * @param ProductDTO[] $productDTOs
     * @param string $csvPath
     *
     * @return void
     */
    public function process(array $productDTOs, string $csvPath): void
    {
        $productRepository = $this->em->getRepository(Product::class);
        $persistedProducts = [];
        $csvRows = [];

        foreach ($productDTOs as $productDTO) {
            $existedProduct = $productRepository->findOneBy(['productUrl' => $productDTO->productUrl]);
            if ($existedProduct) {
                $product = $existedProduct;
            } else {
                $product = new Product();
                $product->setProductUrl($productDTO->productUrl);
            }

            $product->setName($productDTO->name);
            $product->setPrice($productDTO->price);
            $product->setImageUrl($productDTO->imageUrl);

            $persistedProducts[] = $product;
            $csvRows[] = [$productDTO->name, $productDTO->price, $productDTO->imageUrl, $productDTO->productUrl];
        }

        foreach ($persistedProducts as $product) {
            $this->em->persist($product);
        }
        $this->em->flush();

        // Аби не записувати значення по одному, згідно поставленої умови повільності файлової системи, було імплементоване масове записування одним разом.
        $this->csvWriter->writeCsvBatch($csvRows, $csvPath);
    }

}
