<?php

namespace Arachne\Codeception\Util\Connector;

use Nette\DI\Container;
use Nette\Diagnostics\Debugger;
use Nette\Environment;
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
		Environment::setContext($this->container);

		ob_start();
		try {
			// The HTTP code from previous test sometimes survives in http_response_code() so it's necessary to reset it manually.
			// @link https://github.com/nette/nette/pull/1263
			$this->container->getByType('Nette\Http\IResponse')->setCode(IResponse::S200_OK);
			$this->container->getByType('Nette\Application\Application')->run();
		} catch (\Exception $e) {
			ob_end_clean();
			Debugger::log($e);
			throw $e;
		}
		$content = ob_get_clean();

		$httpResponse = $this->container->getByType('Nette\Http\IResponse');
		$code = $httpResponse->getCode();
		$headers = $httpResponse->getHeaders();

		$repsonse = new Response($content, $code, $headers);
		return $repsonse;
	}

}
