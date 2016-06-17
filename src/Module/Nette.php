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
use Arachne\Codeception\Module\Container as ContainerModule;
use Codeception\Lib\Framework;
use Codeception\TestCase;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Nette extends Framework
{
    protected $config = [
        'followRedirects' => true,
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
                $this->container = $this->getModule(ContainerModule::class)->createContainer();
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
