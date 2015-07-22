<?php

namespace Arachne\Codeception\Connector;

use Arachne\Codeception\Http\Request as HttpRequest;
use Arachne\Codeception\Http\Response as HttpResponse;
use Nette\Application\Application;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
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

		$_SERVER['REQUEST_METHOD'] = $method = strtoupper($request->getMethod());
		$_SERVER['REQUEST_URI'] = $uri = str_replace('http://localhost', '', $request->getUri());
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		if ($method === 'HEAD' || $method === 'GET') {
			$_GET = $request->getParameters();
			$_POST = [];
		} else {
			$_POST = $request->getParameters();
			$_GET = [];
		}

		$httpRequest = $this->container->getByType(IRequest::class);
		$httpResponse = $this->container->getByType(IResponse::class);
		if (!$httpRequest instanceof HttpRequest || !$httpResponse instanceof HttpResponse) {
			throw new \Exception('Arachne\Codeception\DI\CodeceptionExtension is not used or conflicts with another extension.');
		}
		$httpRequest->reset();
		$httpResponse->reset();

		try {
			ob_start();
			$this->container->getByType(Application::class)->run();
			$content = ob_get_clean();

		} catch (\Exception $e) {
			ob_end_clean();
			throw $e;
		}

		$code = $httpResponse->getCode();
		$headers = $httpResponse->getHeaders();

		return new Response($content, $code, $headers);
	}

}
