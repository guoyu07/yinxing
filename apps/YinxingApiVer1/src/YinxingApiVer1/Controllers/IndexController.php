<?php


namespace Eva\YinxingApiVer1\Controllers;

use Eva\YinxingApiVer1\Models;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        return $this->response->setJsonContent([]);
    }
}
