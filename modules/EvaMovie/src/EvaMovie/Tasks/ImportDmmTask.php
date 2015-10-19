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
use Eva\EvaMovie\Entities\Makers;
use Eva\EvaMovie\Entities\Movies;
use Eva\EvaMovie\Entities\Series;
use Eva\EvaMovie\Entities\Staffs;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use JpnForPhp\Transliterator\Romaji;
use QueryPath\DOMQuery;
use Symfony\Component\DomCrawler\Crawler;

class ImportDmmTask extends TaskBase
{
    private $currentDmmId;
    private $currentTable;

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
        $this->output->writelnInfo("Importer started.");

        $fileCount = 0;
        $root = '/opt/htdocs/yinxing/modules/EvaMovie/tests/EvaMovieTests';
        $files = new \GlobIterator($root . '/*.html');
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $this->importSingleMovie($file);
            $fileCount++;
        }

        $this->output->writelnSuccess(sprintf("Import process finished, %d file imported", $fileCount));
    }

    public function importSingleMovie(\SplFileInfo $source)
    {
        if (!$source->isReadable()) {
            return $this->output->writelnError(sprintf("File %s not readable", $source->getFilename()));
        }
        $this->output->writelnInfo(sprintf("Importing %s ...", $source->getRealPath()));

        $domCrawler = new Crawler();
        $file = $source->openFile();
        $domCrawler->addContent($file->fread($file->getSize()));

        $dmmId = $domCrawler->filter("#sample-video > a")->eq(0)->attr("id");
        if (!$dmmId) {
            return $this->output->writelnError(sprintf("Not found dmm id", $source->getFilename()));
        }

        $this->currentDmmId = $dmmId;

        $movie = new Movies();
        $movie->id = CrawlDmmTask::dmmMovieIDToYinxingID($dmmId);
        $movie->title = $domCrawler->filter(".page-detail #title")->text();
        $imageLarge = $domCrawler->filter("#sample-video > a")->eq(0)->attr("href");
        $detailTable = $domCrawler->filter(".page-detail table.mg-b20");
        $movie->banngo = $dmmId;
        $movie->subBanngo = $dmmId;
        $movie->alt = '';
        $movie->subtype = '';

        $detailQuery = $this->getDetailQuery('商品発売日：', $dmmId, $detailTable);
        if (count($detailQuery) > 0) {
            $movie->pubdate = str_replace('/', '-', trim($detailQuery->text(), "\n "));
            $movie->year = substr($movie->pubdate, 0, 4);
        }
        $movie->images = $imageLarge;
        $movie->previews = '';
        $detailQuery = $this->getDetailQuery('シリーズ：', $dmmId, $detailTable)->filter('a');
        if (count($detailQuery) > 0) {
            $series = new Series();
            if (preg_match('/id=(\d+)/', $detailQuery->filter('a')->attr('href'), $matches)) {
                $seriesId = $matches[1];
                $series->id = CrawlDmmTask::dmmOtherIDConvert($seriesId);
                $series->name = trim($detailQuery->text());
                $movie->series = $series;
            }
        }

        $detailQuery = $this->getDetailQuery('メーカー：', $dmmId, $detailTable);
        if (count($detailQuery) > 0) {
            $maker = new Makers();
            if (preg_match('/id=(\d+)/', $detailQuery->filter('a')->attr('href'), $matches)) {
                $maker->id = CrawlDmmTask::dmmOtherIDConvert($matches[1]);
                $maker->name = trim($detailQuery->text());
                $movie->maker = $maker;
            }
        }

        $detailQuery = $this->getDetailQuery('出演者：', $dmmId, $detailTable)->filter('a');
        if (count($detailQuery) > 0) {
            $casts = $this->processActress($this->getCasts($detailQuery));
            $movie->casts = $casts;
        }


        var_dump($movie->toArray());
        exit;
    }

    private function getCasts(Crawler $links)
    {
        $staffs = [];
        $links->each(function (Crawler $link, $i) use (&$staffs) {
            if (!preg_match('/id=(\d+)/', $link->attr('href'), $matches)) {
                return;
            }

            $staff = new Staffs();
            $staff->id = CrawlDmmTask::dmmOtherIDConvert($matches[1]);
            $staff->name = trim($link->text());
            $staffs[] = $staff;
        });
        return $staffs;
    }

    private function processActress($casts)
    {
        foreach ($casts as $key => $cast) {
            $cast->gender = 'female';
            $casts[$key] = $cast;
        }
        return $casts;
    }

    private function parseTable($dmmId, Crawler $queryTable)
    {
        if ($dmmId = $this->currentDmmId && $this->currentTable) {
            return $this->currentTable;
        }

        $table = [];
        $queryTable->filter('tr')->each(function (Crawler $row, $i) use (&$table) {
            $td = $row->filter('td');
            if (count($td) < 2) {
                return true;
            }
            $key = trim($td->eq(0)->text(), " \n");
            if (!$key) {
                return true;
            }
            $table[$key] = $td->eq(1);
        });
        return $this->currentTable = $table;
    }

    /**
     * @param $column
     * @param $dmmId
     * @param Crawler $queryTable
     * @return Crawler | null
     */
    private function getDetailQuery($column, $dmmId, Crawler $queryTable)
    {
        $table = $this->parseTable($dmmId, $queryTable);
        if (isset($table[$column])) {
            return $table[$column];
        }
    }
}
