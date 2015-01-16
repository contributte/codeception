<?php

namespace Arachne\Codeception\Connector;

use Nette\DI\Container;
use Nette\Http\IResponse;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

/**
 * @author Jáchym Toušek
 */
class Nette extends Client
{

	/** @var Container */
	protected $container;

	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function doRequest($request)
	{
		$_COOKIE = $request->getCookies();
		$_SERVER = $request->getServer();
		$_FILES = $request->getFiles();

		$uri = str_replace('http://localhost', '', $request->getUri());

		$_SERVER['REQUEST_METHOD'] = strtoupper($request->getMethod());
		$_SERVER['REQUEST_URI'] = $uri;

		// Container initialization can't be called earlier because Nette\Http\IRequest service might be initialized too soon and amOnPage method would not work anymore.
		$this->container->initialize();

		try {
			ob_start();
			$this->container->getByType('Nette\Application\Application')->run();
			$content = ob_get_clean();

		} catch (\Exception $e) {
			ob_end_clean();
			throw $e;
		}

		$httpResponse = $this->container->getByType('Nette\Http\IResponse');
		$code = $httpResponse->getCode();
		$headers = $httpResponse->getHeaders();

		return new Response($content, $code, $headers);
	}

}
