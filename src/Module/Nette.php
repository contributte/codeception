<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Module;

use Arachne\Codeception\Connector\Nette as NetteConnector;
use Codeception\Exception\ModuleConfigException;
use Codeception\TestCase;
use Codeception\Lib\Framework;
use Nette\DI\MissingServiceException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Nette extends Framework
{

	public static $containerClass;

	protected $config = [
		'followRedirects' => true,
	];

	public function _before(TestCase $test)
	{
		if (!class_exists(self::$containerClass)) {
			throw new ModuleConfigException(__CLASS__, 'Specify container class in bootstrap.');
		}
		$this->container = new self::$containerClass();
		$this->container->initialize();
		$this->client = new NetteConnector();
		$this->client->setContainer($this->container);
		$this->client->followRedirects($this->config['followRedirects']);
		parent::_before($test);
	}

	public function _after(TestCase $test)
	{
		parent::_after($test);

		try {
			$this->container->getByType('Nette\Http\Session')->close();
		} catch (MissingServiceException $e) {
		}

		$_SESSION = [];
		$_GET = [];
		$_POST = [];
		$_FILES = [];
		$_COOKIE = [];
	}

	/**
	 * @param string $service
	 * @return object
	 */
	public function grabService($service)
	{
		try {
			return $this->container->getByType($service);
		} catch (MissingServiceException $e) {
			$this->fail($e->getMessage());
		}
	}

	public function seeRedirectTo($url)
	{
		if ($this->config['followRedirects']) {
			$this->fail('Method seeRedirectTo only works when followRedirects option is disabled');
		}
		$request = $this->container->getByType('Nette\Http\IRequest');
		$response = $this->container->getByType('Nette\Http\IResponse');
		if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl() . $url && $response->getHeader('Location') !== $url) {
			$this->fail('Couldn\'t confirm redirect target to be "' . $url . '", Location header contains "' . $response->getHeader('Location') . '".');
		}
	}

	public function debugContent()
	{
		$this->debugSection('Content', $this->client->getInternalResponse()->getContent());
	}

}
