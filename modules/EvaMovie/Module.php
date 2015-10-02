<?php

namespace Eva\EvaMovie;

use Eva\EvaEngine\Module\AbstractModule;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;

class Module extends AbstractModule
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaMovie' => __DIR__ . '/src/EvaMovie',
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
        $dispatcher->setDefaultNamespace('Eva\EvaMovie\Controllers');
    }
}
