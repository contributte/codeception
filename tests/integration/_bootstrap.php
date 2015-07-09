<?php

use Arachne\Bootstrap\Configurator;
use Arachne\Codeception\Module\Nette;
use Nette\Bridges\Framework\NetteExtension;

$configurator = new Configurator;
$configurator->enableDebugger(__DIR__ . '/../_log');
$configurator->setTempDirectory(__DIR__ . '/../_temp');
$configurator->setDebugMode(true);

// Create Dependency Injection container from config.neon file
$section = class_exists(NetteExtension::class) ? 'nette_2.2' : 'nette_2.3';
$configurator->addConfig(__DIR__ . '/config/config.neon', $section);

// Don't use this instance for anything else than console commands!
$container = $configurator->createContainer();
Nette::$containerClass = get_class($container);
return $container;
