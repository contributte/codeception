<?php declare(strict_types = 1);

namespace Contributte\Codeception\Http;

use Nette\Http\FileUpload;
use Nette\Http\IRequest;
use Nette\Http\RequestFactory;
use Nette\Http\UrlScript;

/**
 * HttpRequest class for tests.
 */
class Request implements IRequest
{

	private RequestFactory $factory;

	private IRequest $request;

	public function __construct(RequestFactory $factory)
	{
		$this->factory = $factory;
		$this->reset();
	}

	public function reset(): void
	{
		$this->request = $this->factory->fromGlobals();
	}

	public function getCookie(string $key): mixed
	{
		return $this->request->getCookie($key);
	}

	/**
	 * @return mixed[]
	 */
	public function getCookies(): array
	{
		return $this->request->getCookies();
	}

	public function getFile(string $key): ?FileUpload
	{
		return $this->request->getFile($key);
	}

	/**
	 * @return FileUpload[]
	 */
	public function getFiles(): array
	{
		return $this->request->getFiles();
	}

	public function getHeader(string $header): ?string
	{
		return $this->request->getHeader($header);
	}

	/**
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		return $this->request->getHeaders();
	}

	public function getMethod(): string
	{
		return $this->request->getMethod();
	}

	public function getPost(?string $key = null): mixed
	{
		return func_num_args() === 0 ? $this->request->getPost() : $this->request->getPost($key);
	}

	public function getQuery(?string $key = null): mixed
	{
		return func_num_args() === 0 ? $this->request->getQuery() : $this->request->getQuery($key);
	}

	public function getRawBody(): ?string
	{
		return $this->request->getRawBody();
	}

	public function getRemoteAddress(): ?string
	{
		return $this->request->getRemoteAddress();
	}

	public function getRemoteHost(): ?string
	{
		return $this->request->getRemoteHost();
	}

	public function getUrl(): UrlScript
	{
		return $this->request->getUrl();
	}

	public function isAjax(): bool
	{
		return $this->request->isAjax();
	}

	public function isMethod(string $method): bool
	{
		return $this->request->isMethod($method);
	}

	public function isSecured(): bool
	{
		return $this->request->isSecured();
	}

	public function isSameSite(): bool
	{
		return true;
	}

}
