<?php

namespace App\Utility;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageFetcher
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchPage(string $url): string
    {
        return $this->client->request('GET', $url)->getContent();
    }

    /**
     * Написано розраховуючи на те, що надто багато сторінок не отримуватиметься одразу.
     * В іншому випадку потрібно розбити http запити на порції.
     * https://symfony.com/doc/current/http_client.html#concurrent-requests
     *
     * @throws TransportExceptionInterface
     */
    public function fetchMultiplePages(string $baseUrl, int $pagesCount): array
    {
        $responses = [];
        for ($i = 1; $i <= $pagesCount; ++$i) {
            $url = $baseUrl . '/page=' . $i;
            $responses[$i] = $this->client->request('GET', $url);
        }

        $contents = [];
        foreach ($responses as $i => $response) {
            try {
                $contents[] = $response->getContent();
            } catch (HttpExceptionInterface $e) {
                $this->logger->error("Page $i of $baseUrl failed: " . $e->getMessage());
                continue;
            }
        }

        return $contents;
    }
}
