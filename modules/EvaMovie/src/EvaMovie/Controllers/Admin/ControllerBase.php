<?php

namespace Eva\EvaMovie\Controllers\Admin;

use Eva\EvaEngine\Mvc\Controller\AdminControllerBase as AdminControllerBase;
use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;

class ControllerBase extends AdminControllerBase implements SessionAuthorityControllerInterface
{
    public function initialize()
    {
        $this->view->setModuleViewsDir('EvaMovie', '/views');
        $this->view->setModuleLayout('EvaCommon', '/views/admin/layouts/layout');
        $this->view->setModulePartialsDir('EvaCommon', '/views');
    }
}
