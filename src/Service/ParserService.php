<?php

namespace App\Service;

use App\Exception\Product\ProductValidationException;
use App\Message\BatchProcessProductsMessage;
use App\Utility\CsvWriter;
use App\Utility\PageFetcher;
use App\Validator\Product\ProductDTOValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ParserService
{
    public function __construct(
        private readonly PageFetcher   $fetcher,
        private readonly ProductParserService $productParserService,
        private readonly MessageBusInterface $bus,
        private readonly ProductDTOValidator $validator,
        private readonly CsvWriter $csvWriter,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function parse(string $baseUrl, int $pagesCount): void
    {
        $csvPath = $this->csvWriter->generateCsvPath();

        try {
            $fetchedContents = $this->fetcher->fetchMultiplePages($baseUrl, $pagesCount);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Error while fetching pages: ' . $e->getMessage());
            $fetchedContents = [];
        }

        foreach ($fetchedContents as $content) {
            $validProductDTOs = [];
            $productDTOs = $this->productParserService->parseProducts($content);

            foreach ($productDTOs as $productDTO) {
                try {
                    $this->validator->validate($productDTO);
                } catch (ProductValidationException $e) {
                    // Тут я би відокремлював подібні продукти в окремий стек, та записував би їх в окремий CSV файл та в БД завів би булеве значення 'isValid' та можливо enum поле для конкретної помилки
                    // Для поставленого завдання, сподіваюсь, їх буде достатньо логувати та скіпати.
                    $this->logger->warning('Product data got wrong: ' . $e->getMessage());
                    continue;
                }

                $validProductDTOs[] = $productDTO;
            }

            $this->bus->dispatch(new BatchProcessProductsMessage(
                $validProductDTOs,
                $csvPath
            ));
        }

    }

}
