<?php declare(strict_types = 1);

namespace Contributte\Codeception\Http;

use DateTimeInterface;
use Nette\Http\Helpers;
use Nette\Http\IResponse;
use Nette\Utils\DateTime;

/**
 * HttpResponse class for tests.
 */
class Response implements IResponse
{

	/** @var int */
	private $code = self::S200_OK;

	/** @var string[] */
	private $headers = [];

	public function reset(): void
	{
		$this->code = self::S200_OK;
		$this->headers = [];
	}

	public function getCode(): int
	{
		return $this->code;
	}

	/**
	 * @return static
	 */
	public function setCode(int $code, ?string $reason = null)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return static
	 */
	public function setHeader(string $name, string $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @return static
	 */
	public function addHeader(string $name, string $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @return static
	 */
	public function setContentType(string $type, ?string $charset = null)
	{
		$this->setHeader('Content-Type', $type . ($charset !== null ? '; charset=' . $charset : ''));
		return $this;
	}

	public function redirect(string $url, int $code = self::S302_FOUND): void
	{
		$this->setCode($code);
		$this->setHeader('Location', $url);
	}

	/**
	 * @return static
	 */
	public function setExpiration(?string $time)
	{
		if (!$time) {
			$this->setHeader('Cache-Control', 's-maxage=0, max-age=0, must-revalidate');
			$this->setHeader('Expires', 'Mon, 23 Jan 1978 10:00:00 GMT');

			return $this;
		}

		$time = DateTime::from($time);
		$this->setHeader('Cache-Control', 'max-age=' . ((int) $time->format('U') - time()));
		$this->setHeader('Expires', Helpers::formatDate($time));

		return $this;
	}

	public function isSent(): bool
	{
		return false;
	}

	public function getHeader(string $name): ?string
	{
		return $this->headers[$name] ?? null;
	}

	/**
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * @param string|int|DateTimeInterface $time
	 * @return static
	 */
	public function setCookie(string $name, string $value, $time, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httpOnly = null, ?string $sameSite = null)
	{
		return $this;
	}

	public function deleteCookie(string $name, ?string $path = null, ?string $domain = null, ?bool $secure = null): void
	{
	}

}
