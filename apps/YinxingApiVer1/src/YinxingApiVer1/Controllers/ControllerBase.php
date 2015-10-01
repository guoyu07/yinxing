<?php

namespace Eva\YinxingApiVer1\Controllers;

use Eva\EvaEngine\Mvc\Controller\ControllerBase as EngineControllerBase;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;

class ControllerBase extends EngineControllerBase implements JsonControllerInterface
{
    public function initialize()
    {
        $this->view->disable();
    }

}
