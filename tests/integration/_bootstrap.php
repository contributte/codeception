<?php

use Arachne\Bootstrap\Configurator;
use Arachne\Codeception\Module\Nette;

$configurator = new Configurator();
$configurator->enableDebugger(__DIR__.'/../_log');
$configurator->setTempDirectory(__DIR__.'/../_temp');
$configurator->setDebugMode(true);

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__.'/config/config.neon', false);

// Don't use this instance for anything else than console commands!
$container = $configurator->createContainer();
Nette::$containerClass = get_class($container);

return $container;
