<?php

namespace Arachne\Codeception\Module;

use Arachne\Codeception\Connector\Nette as NetteConnector;
use Codeception\TestCase;
use Codeception\Lib\Framework;
use Nette\DI\MissingServiceException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Nette extends Framework
{

	public static $containerClass;

	public function _before(TestCase $test)
	{
		if (!class_exists(self::$containerClass)) {
			throw new \Codeception\Exception\ModuleConfig(__CLASS__, 'Specify container class in bootstrap.');
		}
		$this->container = new self::$containerClass();
		$this->client = new NetteConnector();
		$this->client->setContainer($this->container);
		// TODO: make this configurable
		$this->client->followRedirects(false);
		parent::_before($test);
	}

	public function _after(TestCase $test)
	{
		parent::_after($test);
		$_SESSION = [];
		$_GET = [];
		$_POST = [];
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
