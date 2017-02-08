<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Module;

use Codeception\Module;
use Codeception\TestInterface;
use Nette\Caching\Storages\IJournal;
use Nette\Caching\Storages\SQLiteJournal;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Http\Session;
use Nette\Utils\FileSystem;
use ReflectionProperty;

class NetteDIModule extends Module
{
    /**
     * @var callable[]
     */
    public $onCreateContainer = [];

    protected $config = [
        'configFiles' => [],
        'appDir' => null,
        'logDir' => null,
        'wwwDir' => null,
        'debugMode' => null,
        'removeDefaultExtensions' => false,
    ];

    protected $requiredFields = [
        'tempDir',
    ];

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $configFiles;

    /**
     * @var Container|null
     */
    private $container;

    public function _beforeSuite($settings = [])
    {
        $this->path = $settings['path'];
    }

    public function _before(TestInterface $test)
    {
        $tempDir = $this->path.'/'.$this->config['tempDir'];
        FileSystem::delete(realpath($tempDir));
        FileSystem::createDir($tempDir);
        $this->container = null;
    }

    public function _after(TestInterface $test)
    {
        if ($this->container) {
            try {
                $this->container->getByType(Session::class)->close();
            } catch (MissingServiceException $e) {
            }

            try {
                $journal = $this->container->getByType(IJournal::class);
                if ($journal instanceof SQLiteJournal) {
                    $property = new ReflectionProperty(SQLiteJournal::class, 'pdo');
                    $property->setAccessible(true);
                    $property->setValue($journal, null);
                }
            } catch (MissingServiceException $e) {
            }

            FileSystem::delete(realpath($this->container->getParameters()['tempDir']));
        }
    }

    public function useConfigFiles(array $configFiles)
    {
        if ($this->container) {
            $this->fail('Can\'t set configFiles after the container is created.');
        }
        $this->configFiles = $configFiles;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->createContainer();
        }

        return $this->container;
    }

    /**
     * @param string $service
     *
     * @return object
     */
    public function grabService($service)
    {
        try {
            return $this->getContainer()->getByType($service);
        } catch (MissingServiceException $e) {
            $this->fail($e->getMessage());
        }
    }

    private function createContainer()
    {
        $configurator = new Configurator();
        if ($this->config['removeDefaultExtensions']) {
            $configurator->defaultExtensions = [
                'extensions' => 'Nette\DI\Extensions\ExtensionsExtension',
            ];
        }

        if ($this->config['logDir']) {
            $logDir = $this->path.'/'.$this->config['logDir'];
            FileSystem::createDir($logDir);
            $configurator->enableDebugger($logDir);
        }

        $configurator->addParameters([
            'appDir' => $this->path.($this->config['appDir'] ? '/'.$this->config['appDir'] : ''),
            'wwwDir' => $this->path.($this->config['wwwDir'] ? '/'.$this->config['wwwDir'] : ''),
        ]);

        $tempDir = $this->path.'/'.$this->config['tempDir'];
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        $configurator->setTempDirectory($tempDir);

        if ($this->config['debugMode'] !== null) {
            $configurator->setDebugMode($this->config['debugMode']);
        }

        $configFiles = is_array($this->configFiles) ? $this->configFiles : $this->config['configFiles'];
        foreach ($configFiles as $file) {
            $configurator->addConfig($this->path.'/'.$file);
        }

        $this->container = $configurator->createContainer();

        foreach ($this->onCreateContainer as $callback) {
            $callback($this->container);
        }
    }
}
