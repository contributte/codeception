<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Module;

use Arachne\Codeception\Connector\Nette as NetteConnector;
use Codeception\Lib\Framework;
use Codeception\TestCase;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Utils\FileSystem;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Nette extends Framework
{
    protected $config = [
        'followRedirects' => true,
        'configFiles' => [],
        'logDir' => null,
        'debugMode' => null,
        'configurator' => Configurator::class,
    ];

    protected $requiredFields = [
        'tempDir',
    ];

    /**
     * @var array
     */
    private $configFiles;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var callable
     */
    private $containerAccessor;

    /**
     * @var string
     */
    private $path;

    public function _beforeSuite($settings = [])
    {
        $this->path = $settings['path'];
    }

    public function _before(TestCase $test)
    {
        $this->configFiles = null;
        $this->container = null;
        $this->containerAccessor = function () {
            if (!$this->container) {
                $configurator = new $this->config['configurator']();

                if ($this->config['logDir']) {
                    $configurator->enableDebugger($this->path.'/'.$this->config['logDir']);
                }

                $tempDir = $this->path.'/'.$this->config['tempDir'];
                FileSystem::delete($tempDir);
                FileSystem::createDir($tempDir);
                $configurator->setTempDirectory($tempDir);

                if ($this->config['debugMode'] !== null) {
                    $configurator->setDebugMode($this->config['debugMode']);
                }

                $configFiles = is_array($this->configFiles) ? $this->configFiles : $this->config['configFiles'];
                foreach ($configFiles as $file) {
                    $configurator->addConfig($this->path.'/'.$file, false);
                }

                $this->container = $configurator->createContainer();
            }

            return $this->container;
        };

        $this->client = new NetteConnector();
        $this->client->setContainerAccessor($this->containerAccessor);
        $this->client->followRedirects($this->config['followRedirects']);

        parent::_before($test);
    }

    public function useConfigFiles(array $configFiles)
    {
        if ($this->container) {
            $this->fail('Can\'t set configFiles after the container is created.');
        }
        $this->configFiles = $configFiles;
    }

    public function _after(TestCase $test)
    {
        parent::_after($test);

        if ($this->container) {
            try {
                $this->container->getByType('Nette\Http\Session')->close();
            } catch (MissingServiceException $e) {
            }

            FileSystem::delete($this->container->getParameters()['tempDir']);
        }

        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
    }

    /**
     * @param string $service
     *
     * @return object
     */
    public function grabService($service)
    {
        try {
            return call_user_func($this->containerAccessor)->getByType($service);
        } catch (MissingServiceException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function seeRedirectTo($url)
    {
        if ($this->config['followRedirects']) {
            $this->fail('Method seeRedirectTo only works when followRedirects option is disabled');
        }
        $container = call_user_func($this->containerAccessor);
        $request = $container->getByType('Nette\Http\IRequest');
        $response = $container->getByType('Nette\Http\IResponse');
        if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl().$url && $response->getHeader('Location') !== $url) {
            $this->fail('Couldn\'t confirm redirect target to be "'.$url.'", Location header contains "'.$response->getHeader('Location').'".');
        }
    }

    public function debugContent()
    {
        $this->debugSection('Content', $this->client->getInternalResponse()->getContent());
    }
}
