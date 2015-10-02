<?php

namespace Eva\EvaMovie\Controllers;

use Eva\EvaEngine\Mvc\Controller\ControllerBase as EngineControllerBase;

class ControllerBase extends EngineControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaMovie', '/views/layouts/default');
        $this->view->setModuleViewsDir('EvaMovie', '/views');
        $this->view->setModulePartialsDir('EvaMovie', '/views');
    }

}
