<?php

namespace App\MessageHandler;

use App\Message\BatchProcessProductsMessage;
use App\Service\ProductProcessorService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BatchProductMessageHandler
{
    public function __construct(
        private readonly ProductProcessorService $productProcessor
    ) {
    }

    public function __invoke(BatchProcessProductsMessage $message): void
    {
        $this->productProcessor->process($message->products, $message->csvPath);
    }
}
