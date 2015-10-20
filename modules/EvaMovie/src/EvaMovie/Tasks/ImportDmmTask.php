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
use Symfony\Component\DomCrawler\Crawler;
use Phalcon\Mvc\Model\Transaction\Manager as TranscationManager;


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
        $files = new \GlobIterator($root . '/_html/mird00143.html');
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

        $file = $source->openFile();
        /** @var Movies $movie */
        $movie = $this->getMovie($file->fread($file->getSize()));

        if (!$movie) {
            return $this->output->writelnError(sprintf("Not found dmm id", $source->getFilename()));
        }

        $this->currentDmmId = $movie->banngo;

        if (Movies::findFirstById($movie->id)) {
            return $this->output->writelnWarning(sprintf("Movie %s already exists, insert skipped", $movie->banngo));
        }

        /** @var TranscationManager $tm */
        $tm = $this->getDI()->get('transactions');
        $transcation = $tm->get();
        $movie->setTransaction($transcation);
        if (false === $movie->save()) {
            $transcation->rollback();
            return $this->output->writelnError(sprintf(
                "Movie saving failed by %s",
                implode(',', $movie->getMessages())
            ));
        }
        $transcation->commit();
        $this->output->writelnSuccess(sprintf("Movie saving success", $movie->banngo));
    }

    public function getMovie($html)
    {
        $domCrawler = new Crawler();
        $movie = new Movies();
        $domCrawler->addContent($html);
        $dmmId = $domCrawler->filter("#sample-video > a")->eq(0)->attr("id");
        if (!$dmmId) {
            return;
        }

        $movie->id = CrawlDmmTask::dmmMovieIDToYinxingID($dmmId);
        $movie->title = $domCrawler->filter(".page-detail #title")->text();
        $detailTable = $domCrawler->filter(".page-detail table.mg-b20");
        $movie->banngo = $dmmId;
        $movie->subBanngo = $dmmId;
        $movie->alt = "http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=$dmmId/";
        $movie->subtype = '';

        $detailQuery = $this->getDetailQuery('商品発売日：', $dmmId, $detailTable);
        if (count($detailQuery) > 0) {
            $movie->pubdate = str_replace('/', '-', trim($detailQuery->text(), "\n "));
            $movie->year = substr($movie->pubdate, 0, 4);
        }

        $detailQuery = $this->getDetailQuery('収録時間：', $dmmId, $detailTable);
        if (count($detailQuery) > 0 && preg_match('/(\d+)分/', $detailQuery->text(), $matches)) {
            $movie->durations = $matches[1];
        }


        $detailQuery = $domCrawler->filter(".page-detail .mg-b20.lh4");
        if (count($detailQuery) > 0) {
            foreach ($detailQuery->children() as $childNode) {
                $childNode->parentNode->removeChild($childNode);
            }
            $summary = trim($detailQuery->text());
            $movie->summary = $summary ?: null;
        }


        $detailQuery = $domCrawler->filter("#sample-video > a");
        if (count($detailQuery) > 0) {
            $image = $detailQuery->first()->attr('href');
            $images = [str_replace('pl.', 'pt.', $image), str_replace('pl.', 'ps.', $image), $image];
            $movie->images = implode(',', $images);
        }

        $detailQuery = $domCrawler->filter("#sample-image-block img");
        if (count($detailQuery) > 0) {
            $previews = [];
            /** @var \DOMElement $img */
            foreach ($detailQuery as $img) {
                $previews[] = $img->getAttribute('src');
            }
            $movie->previews = implode(',', $previews);
        }

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
            $movie->casts = $this->processActress($this->getCasts($detailQuery));
        }

        $detailQuery = $this->getDetailQuery('監督：', $dmmId, $detailTable)->filter('a');
        if (count($detailQuery) > 0) {
            $directors = $this->processDirector($this->getCasts($detailQuery));
            //Phalcon many to many will be convert to 1:1 if result set has only one result
            $movie->directors = $directors;
        }

        $detailQuery = $this->getDetailQuery('ジャンル：', $dmmId, $detailTable)->filter('a');
        if (count($detailQuery) > 0) {
            $tags = [];
            $detailQuery->each(function (Crawler $link, $i) use (&$tags) {
                $tags[] = trim($link->text());
            });
            $movie->tags = implode(',', $tags);
        }

        return $movie;
    }

    private function getCasts(Crawler $links)
    {
        $staffs = [];
        $links->each(function (Crawler $link, $i) use (&$staffs) {
            if (!preg_match('/id=(\d+)/', $link->attr('href'), $matches)) {
                return;
            }

            $staff = new Staffs();
            $staff->name = trim($link->text());
            $staff->id = $matches[1];
            $staffs[] = $staff;
        });

        return $staffs;
    }

    private function processActress($casts)
    {
        /** @var Staffs $cast */
        foreach ($casts as $key => $cast) {
            $cast->id = CrawlDmmTask::dmmOtherIDConvert($cast->id);
            $cast->gender = 'female';
            $casts[$key] = $cast;
        }
        return $casts;
    }

    private function processDirector($casts)
    {
        /** @var Staffs $cast */
        foreach ($casts as $key => $cast) {
            $cast->id = CrawlDmmTask::dmmDirectorIDConvert($cast->id);
            $cast->isDirector = 1;
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
