<?php

namespace App\Command;

use App\Service\ParserServiceRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'parse:general',
    description: 'Run all available parsers',
)]
class GeneralParserCommand extends Command
{
    private ParserServiceRegistry $parserServiceRegistry;

    public function __construct(ParserServiceRegistry $parserServiceRegistry)
    {
        parent::__construct();

        $this->parserServiceRegistry = $parserServiceRegistry;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parserServices = $this->parserServiceRegistry->getParserService();

        foreach ($parserServices as $parserService) {
            $parserService->handle();
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Parsers completed work successfully');

        return Command::SUCCESS;
    }
}
