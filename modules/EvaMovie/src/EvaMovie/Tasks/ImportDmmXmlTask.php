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

class ImportDmmXmlTask extends TaskBase
{
    use DmmXmlTrait;

    public function mainAction()
    {
        if (PHP_INT_SIZE === 4) {
            return $this->output->writelnError("Require PHP 64bit to run this script by CRC32 issue");
        }
        $this->output->writelnInfo("Import started.");

        $fileCount = 0;
        $root = '/opt/htdocs/yinxing_dmm/dl';
        $files = new \GlobIterator($root . '/*.xml');
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $this->importXmlFile($file);
            $fileCount++;
        }

        $this->output->writelnSuccess(sprintf("Import process finished, %d file imported", $fileCount));
    }

    public function importXmlFile(\SplFileInfo $source)
    {
        if (!$source->isReadable()) {
            return $this->output->writelnError(sprintf("File %s not readable", $source->getFilename()));
        }
        $this->output->writelnInfo(sprintf("Importing %s ...", $source->getRealPath()));

        $file = $source->openFile();
        $xml = simplexml_load_string($file->fread($file->getSize()));

        return $this->saveDmmList($xml);
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
}
