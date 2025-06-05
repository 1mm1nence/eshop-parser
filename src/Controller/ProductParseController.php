<?php

namespace App\Controller;

use App\Command\ParseProductsCommand;
use App\Service\ParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductParseController extends AbstractController
{
    public function __construct(
        private readonly ParserService $parserService,
    )
    {
    }

    #[Route('/parse_manual', name: 'parse_manual')]
    public function parseManual(Request $request): Response
    {
        $url = $request->query->get('url', ParseProductsCommand::DEFAULT_LINK);
        $pagesCount = (int) $request->query->get('pagesCount', ParseProductsCommand::DEFAULT_PAGES_COUNT);

        $this->parserService->parse($url, $pagesCount);

        $responseText = $pagesCount . ' pages of ' . $url . ' are in process of exporting to CSV and database.';
        return new Response($responseText);
    }
}