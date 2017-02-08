<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Module;

use Arachne\Codeception\Connector\NetteConnector;
use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class NetteApplicationModule extends Framework
{
    /**
     * @var NetteConnector
     */
    public $client;

    /**
     * @var array
     */
    protected $config = [
        'followRedirects' => true,
    ];

    /**
     * @var string
     */
    private $path;

    public function _beforeSuite($settings = [])
    {
        $this->path = $settings['path'];
    }

    public function _before(TestInterface $test)
    {
        $this->client = new NetteConnector();
        $this->client->setContainerAccessor(
            function () {
                /** @var NetteDIModule $diModule */
                $diModule = $this->getModule(NetteDIModule::class);

                return $diModule->getContainer();
            }
        );
        $this->client->followRedirects($this->config['followRedirects']);

        parent::_before($test);
    }

    public function _after(TestInterface $test)
    {
        parent::_after($test);

        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
    }

    /**
     * @param bool $followRedirects
     */
    public function followRedirects($followRedirects)
    {
        $this->client->followRedirects($followRedirects);
    }

    /**
     * @param string $url
     */
    public function seeRedirectTo($url)
    {
        if ($this->client->isFollowingRedirects()) {
            $this->fail('Method seeRedirectTo only works when followRedirects option is disabled');
        }
        /** @var NetteDIModule $diModule */
        $diModule = $this->getModule(NetteDIModule::class);
        $request = $diModule->grabService(IRequest::class);
        $response = $diModule->grabService(IResponse::class);
        if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl().$url && $response->getHeader('Location') !== $url) {
            $this->fail('Couldn\'t confirm redirect target to be "'.$url.'", Location header contains "'.$response->getHeader('Location').'".');
        }
    }

    public function debugContent()
    {
        $this->debugSection('Content', $this->client->getInternalResponse()->getContent());
    }
}
