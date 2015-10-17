<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaMovie\Tasks;

use Eva\EvaEngine\Exception\InvalidArgumentException;
use Eva\EvaEngine\Exception\RuntimeException;
use Eva\EvaEngine\Tasks\TaskBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;

class CrawlDmmTask extends TaskBase
{
    use DmmXmlTrait;

    protected $page = 1;

    protected $perPage = 100;

    protected $maxRetry = 30;

    protected $maxPage = 0;

    protected $client;


    /**
     * Args:
     * --page
     * --force
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function mainAction()
    {
        if (PHP_INT_SIZE === 4) {
            return $this->output->writelnError("Require PHP 64bit to run this script by CRC32 issue");
        }
        $this->output->writelnInfo("Crawl started.");


        //TODO: add write log to save last run time & failed
        if (!$this->dmmHasUpdated()) {
            //TODO: add --force arg
            return $this->output->writelnSuccess(sprintf(
                "Crawl stopped by latest movie %d (%s) already in database",
                self::$yinxingMovieId,
                self::$dmmMovieId
            ));
        }

        $retry = 0;
        while ($retry < $this->maxRetry && $this->page < $this->getMaxPage()) {
            $response = $this->crawlDmm();
            if (false === $response) {
                $retry++;
                $this->output->writelnWarning(sprintf("Request failed, retry times:%d", $retry));
                continue;
            }

            //TODO: add sleep
            $itemList = simplexml_load_string($response->getBody());
            $saveRes = $this->saveDmmList($itemList);
            //TODO: add --force arg
            if (false === $saveRes) {
                $this->output->writelnSuccess(sprintf("Crawl stopped by all save skiped in page %d", $this->page));
                break;
            }
        }
    }

    public function dmmHasUpdated()
    {
        $response = $this->dmmApiCall([
            'page' => 1,
            'perPage' => 1,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException("Get dmm info failed");
        }
        $res = simplexml_load_string($response->getBody());
        if (!isset($res->result->total_count) || !isset($res->result->items->item)) {
            throw new RuntimeException("Get dmm response format not save as expected");
        }
        $this->maxPage = ceil((int)$res->result->total_count / $this->perPage);
        $this->output->writelnInfo(sprintf("Crawling max page %d.", $this->maxPage));

        return !$this->checkItemExist($res->result->items->item[0]);
    }

    protected function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * @param \SimpleXMLElement $items
     * @return bool return false if all save skiped
     * @throws InvalidArgumentException
     */
    protected function saveDmmList(\SimpleXMLElement $items)
    {
        if (!isset($items->result->items->item)) {
            throw new InvalidArgumentException("Dmm response format not same as expected");
        }
        $items = $items->result->items->item;
        $skipTimes = 0;
        foreach ($items as $item) {
            if ($this->checkItemExist($item)) {
                $this->output->writelnComment(sprintf(
                    "Item %d already existed, movie %s not save",
                    self::$yinxingMovieId,
                    self::$dmmMovieId
                ));
                $skipTimes++;
                continue;
            }
            $saveRes = $this->saveDmmItem($item);
            if ($saveRes) {
                $this->output->writelnSuccess(sprintf(
                    "Movie %s saved success as item %d",
                    self::$dmmMovieId,
                    self::$yinxingMovieId
                ));
            } else {
                $this->output->writelnWarning(sprintf(
                    "Movie %s save failed from item %d, reason: %s",
                    self::$dmmMovieId,
                    self::$yinxingMovieId,
                    implode('|', $this->lastDbMessage)
                ));
            }
        }

        $this->output->writelnComment(sprintf(
            "Item list save finished, skiped times: %d, perPage: %d",
            $skipTimes,
            $this->perPage
        ));
        return $skipTimes !== ($this->perPage - 1);
    }

    protected function getClient()
    {
        return $this->client ?: $this->client = new Client([
            'debug' => 1,
            'connect_timeout' => 3
        ]);
    }


    public function dmmApiCall(array $params = [])
    {
        $params = array_merge([
            'page' => 1,
            'service' => 'digital',
            'keyword' => '',
            'perPage' => 100,
            'site' => 'DMM.co.jp',
            'sort' => 'date',
        ], $params);
        $offset = ($params['page'] - 1) * $params['perPage'] + 1;
        return $this->getClient()->get('http://affiliate-api.dmm.com/', [
            'query' => array_merge([
                'api_id' => '',
                'affiliate_id' => '',
                'operation' => 'ItemList',
                'version' => '2.0',
                'timestamp' => date('Y-m-d H:i:s'),
                'site' => $params['site'],
                'service' => $params['service'],
                'offset' => $offset,
                'hits' => $params['perPage'],
                'keyword' => $params['keyword']
            ], [
                'api_id' => $this->getConfig()->movie->crawl->dmm->apiId,
                'affiliate_id' => $this->getConfig()->movie->crawl->dmm->affiliateId,
            ])
        ]);
    }

    protected function crawlDmm()
    {
        try {
            $this->output->writelnInfo(sprintf("Crawling page %d.", $this->page));
            $response = $this->dmmApiCall([
                'page' => $this->page,
                'perPage' => $this->perPage,
            ]);
        } catch (TransferException $e) {
            $this->output->writelnError(sprintf("Request failed for %s", $e));
            return false;
        } catch (ConnectException $e) {
            $this->output->writelnError(sprintf("Request failed for %s", $e));
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            $this->output->writelnError(sprintf(
                "Request failed for %s,  %s",
                $response->getStatusCode(),
                $response->getBody()
            ));
            return false;
        }
        $this->page++;
        return $response;
    }
}
