<?php

namespace Eva\YinxingApiVer1;

use Eva\EvaEngine\Module\AbstractModule;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;

class Module extends AbstractModule
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\YinxingApiVer1' => __DIR__ . '/src/YinxingApiVer1',
        );
    }

    /**
     * Registers the module-only services
     *
     * @param \Phalcon\DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        $dispatcher = $di->getDispatcher();
        $dispatcher->setDefaultNamespace('Eva\YinxingApiVer1\Controllers');
    }
}
