<?php

namespace App\Service\WorkUa;

use App\Dto\WorkUa\JobDto;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class WorkUaParsingHandler
{
    /**
     * @return int[]
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws StrictException
     */
    public function parseJobIds(int $maxPage): array
    {
        $page = 1;
        $jobIds = [];

        while (true) {
            $jobIdsByPage = $this->parseJobIdsByPage($page);
            $jobIds = array_merge($jobIds, $jobIdsByPage);

            if ($page === $maxPage) {
                break;
            }

            sleep(5);
            $page++;
        }

        return array_unique($jobIds);
    }

    /**
     * @return int[]
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws StrictException
     */
    public function parseJobIdsByPage(int $page): array
    {
        $jobIds = [];

        $htmlContent = file_get_contents('https://www.work.ua/jobs-lutsk?page=' . $page);
        $dom = new Dom();
        $dom->loadStr($htmlContent);

        $cards = $dom->find('.card-hover');
        foreach ($cards as $card) {
            $titleElement = $card->find('h2 a');
            $link = $titleElement->getAttribute('href');
            $jobIds[] = (int) filter_var($link, FILTER_SANITIZE_NUMBER_INT);
        }

        return $jobIds;
    }

    /**
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws ContentLengthException
     * @throws LogicalException
     */
    public function parseJobById(int $jobId): JobDto
    {
        $jobDto = new JobDto();
        $jobDto->setExternalId($jobId);

        $htmlContent = file_get_contents('https://www.work.ua/jobs/' . $jobId);
        $dom = new Dom();
        $dom->loadStr($htmlContent);

        $titleElement = $dom->find('#h1-name')[0];
        $jobDto->setTitle($titleElement->text);

        $textElement = $dom->find('#job-description')[0];
        $jobDto->setText($textElement->innerText);

        $companyElement = $dom->find('.card .text-indent a b');
        if (isset($companyElement[0])) {
            $companyElement = $companyElement[0];
            $jobDto->setCompany($companyElement->text);
        }
        $salaryElement = $dom->find('.card .text-indent b');
        if (isset($salaryElement[0])) {
            $salaryElement = $salaryElement[0];
            $salaryDirt = $salaryElement->text;
            $jobDto->setSalary(
                preg_replace("/&(#\d+|\w+);/i", '', $salaryDirt)
            );
        }

        return $jobDto;
    }
}
