<?php
if(!extension_loaded('phalcon')) {
    die('Phalcon extension not loaded');
}
/** @var Composer\Autoload\ClassLoader $loader */
$loader = include __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4("Eva\\EvaMovie\\", __DIR__ . '/../src/EvaMovie');
$loader->addClassMap(["Eva\\EvaMovie\\Module" => __DIR__ . '/../Module.php']);
