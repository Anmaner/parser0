<?php

namespace App\Service\WorkUa;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Service\ParserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class WorkUaParserService implements ParserServiceInterface
{
    private const PARSER_NAME = 'work_ua';
    public const MAX_PAGE = 3;

    private WorkUaParsingHandler $workUaParser;
    private JobRepository $jobRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        WorkUaParsingHandler $workUaParser,
        JobRepository $jobRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->workUaParser = $workUaParser;
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
    }

    public function getName(): string
    {
        return self::PARSER_NAME;
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws StrictException
     * @throws LogicalException
     */
    public function handle(): void
    {
        $jobIds = $this->workUaParser->parseJobIds(self::MAX_PAGE);

        $jobIds = $this->getMissingIds($jobIds);

        foreach ($jobIds as $jobId) {
            $jobDto = $this->workUaParser->parseJobById($jobId);

            $job = new Job();
            $job->setExternalId($jobDto->getExternalId());
            $job->setTitle($jobDto->getTitle());
            $job->setCompany($jobDto->getCompany());
            $job->setSalary($jobDto->getSalary());
            $job->setText($jobDto->getText());

            $this->entityManager->persist($job);
        }

        $this->entityManager->flush();
    }

    private function getMissingIds(array $jobIds): array
    {
        $allJobs = $this->jobRepository->findAll();

        $existingJobIds = array_map(function (Job $job): int {
            return (int)$job->getExternalId();
        }, $allJobs);

        return array_diff($jobIds, $existingJobIds);
    }
}
