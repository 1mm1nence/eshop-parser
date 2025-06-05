<?php

namespace App\Command;

use App\Service\ParserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:parse:rozetka')]
class ParseProductsCommand extends Command
{
    public const DEFAULT_LINK = 'https://hard.rozetka.com.ua/ua/hard/c80026';
    public const DEFAULT_PAGES_COUNT = 3;

    public function __construct(
        private readonly ParserService $parserService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('baseUrl', InputArgument::OPTIONAL, 'Base category URL', self::DEFAULT_LINK)
            ->addArgument('pagesCount', InputArgument::OPTIONAL, 'Number of pages to parse', self::DEFAULT_PAGES_COUNT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseUrl = rtrim($input->getArgument('baseUrl'), '/');
        $pagesCount = (int) $input->getArgument('pagesCount');

        $output->writeln("Starting parsing first {$pagesCount} pages  of {$baseUrl}...");

        $this->parserService->parse($baseUrl, $pagesCount);

        $output->writeln("Parsing finished. Pls check logs at var/log to see if some errors occurred");

        return Command::SUCCESS;
    }
}
