<?php


namespace Eva\YinxingApiVer1\Controllers;

use Eva\EvaMovie\Entities\Movies;
use Eva\EvaMovie\Tasks\CrawlDmmTask;
use Eva\YinxingApiVer1\Models;
use GuzzleHttp\Client;

class IndexController extends ControllerBase
{
    public function indexAction()
    {

        $movies = Movies::find([
            'limit' => 100
        ]);
        return $this->response->setJsonContent($movies->toArray());
    }

    public function testAction()
    {

        $task = new CrawlDmmTask();
        $response = $task->dmmApiCall([
            'page' => 1,
            'perPage' => 1,
        ]);
        return $this->response->setJsonContent(simplexml_load_string($response->getBody()));
    }
}
