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
        $this->configFiles = null;
        $this->client = new NetteConnector();
        $this->client->setContainerAccessor(function () {
            return $this->getModule(NetteDIModule::class)->getContainer();
        });
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

    public function seeRedirectTo($url)
    {
        if ($this->config['followRedirects']) {
            $this->fail('Method seeRedirectTo only works when followRedirects option is disabled');
        }
        $request = $this->getModule(NetteDIModule::class)->grabService(IRequest::class);
        $response = $this->getModule(NetteDIModule::class)->grabService(IResponse::class);
        if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl().$url && $response->getHeader('Location') !== $url) {
            $this->fail('Couldn\'t confirm redirect target to be "'.$url.'", Location header contains "'.$response->getHeader('Location').'".');
        }
    }

    public function debugContent()
    {
        $this->debugSection('Content', $this->client->getInternalResponse()->getContent());
    }
}
