<?php declare(strict_types = 1);

namespace Contributte\Codeception\Connector;

use Contributte\Codeception\Http\Request as HttpRequest;
use Contributte\Codeception\Http\Response as HttpResponse;
use Exception;
use Nette\Application\Application;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use Throwable;

class NetteConnector extends Client
{

	/** @var callable */
	protected $containerAccessor;

	public function setContainerAccessor(callable $containerAccessor): void
	{
		$this->containerAccessor = $containerAccessor;
	}

	/**
	 * @param Request $request
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function doRequest($request): Response
	{
		$_COOKIE = $request->getCookies();
		$_SERVER = $request->getServer();
		$_FILES = $request->getFiles();

		$_SERVER['REQUEST_METHOD'] = $method = strtoupper($request->getMethod());
		$_SERVER['REQUEST_URI'] = str_replace('http://localhost', '', $request->getUri());
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		if ($method === 'HEAD' || $method === 'GET') {
			$_GET = $request->getParameters();
			$_POST = [];
		} else {
			$_GET = [];
			$_POST = $request->getParameters();
		}

		/** @var Container $container */
		$container = ($this->containerAccessor)();

		$httpRequest = $container->getByType(IRequest::class);
		$httpResponse = $container->getByType(IResponse::class);
		if (!$httpRequest instanceof HttpRequest || !$httpResponse instanceof HttpResponse) {
			throw new Exception('Contributte\Codeception\DI\HttpExtension is not used or conflicts with another extension.');
		}

		$httpRequest->reset();
		$httpResponse->reset();

		try {
			ob_start();
			$container->getByType(Application::class)->run();
			$content = (string) ob_get_clean();
		} catch (Throwable $e) {
			ob_end_clean();
			throw $e;
		}

		$code = $httpResponse->getCode();
		$headers = $httpResponse->getHeaders();

		return new Response($content, $code, $headers);
	}

}
