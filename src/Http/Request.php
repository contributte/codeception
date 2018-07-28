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

	/** @var RequestFactory */
	private $factory;

	/** @var IRequest */
	private $request;

	public function __construct(RequestFactory $factory)
	{
		$this->factory = $factory;
		$this->reset();
	}

	public function reset(): void
	{
		$this->request = $this->factory->createHttpRequest();
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getCookie($key, $default = null)
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

	/**
	 * @param string $key
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getFile($key): ?FileUpload
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

	/**
	 * @param string $header
	 * @param string|null $default
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getHeader($header, $default = null): ?string
	{
		return $this->request->getHeader($header, $default);
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

	/**
	 * @param string|null $key
	 * @param mixed $default
	 * @return mixed
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getPost($key = null, $default = null)
	{
		if (func_num_args() === 0) {
			return $this->request->getPost();
		} else {
			return $this->request->getPost($key, $default);
		}
	}

	/**
	 * @param string|null $key
	 * @param mixed $default
	 * @return mixed
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getQuery($key = null, $default = null)
	{
		if (func_num_args() === 0) {
			return $this->request->getQuery();
		} else {
			return $this->request->getQuery($key, $default);
		}
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

	/**
	 * @param string $method
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isMethod($method): bool
	{
		return $this->request->isMethod($method);
	}

	public function isSecured(): bool
	{
		return $this->request->isSecured();
	}

}
