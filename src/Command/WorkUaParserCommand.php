<?php

namespace App\Command;

use App\Service\WorkUa\WorkUaParserService;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'parse:work-ua',
    description: 'Run work-ua parser',
)]
class WorkUaParserCommand extends Command
{
    private WorkUaParserService $workUaService;

    public function __construct(WorkUaParserService $workUaService)
    {
        parent::__construct();

        $this->workUaService = $workUaService;
    }

    protected function configure(): void
    {
    }

    /**
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws ContentLengthException
     * @throws LogicalException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->workUaService->handle();

        $io = new SymfonyStyle($input, $output);
        $io->success('Work ua is successfully parsed');

        return Command::SUCCESS;
    }
}
