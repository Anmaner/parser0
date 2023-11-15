<?php

declare(strict_types=1);

namespace App\Service;

class ParserServiceRegistry
{
    /**
     * @var ParserServiceInterface[]
     */
    private array $parserServices = [];

    public function __construct(iterable $parserServices)
    {
        foreach ($parserServices as $parserService) {
            $this->addParserService($parserService);
        }
    }

    public function addParserService(ParserServiceInterface $parserService): void
    {
        $this->parserServices[$parserService->getName()] = $parserService;
    }

    public function getParserService(): array
    {
        return $this->parserServices;
    }
}
