<?php

declare(strict_types=1);

namespace App\Service;

interface ParserServiceInterface
{
    public function getName(): string;

    public function handle(): void;
}
