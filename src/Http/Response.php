<?php declare(strict_types = 1);

namespace Contributte\Codeception\Http;

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
	 * @param int $code
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setCode($code): self
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setHeader($name, $value): self
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function addHeader($name, $value): self
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @param string      $type
	 * @param string|null $charset
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setContentType($type, $charset = null): self
	{
		$this->setHeader('Content-Type', $type . ($charset !== null ? '; charset=' . $charset : ''));
		return $this;
	}

	/**
	 * @param string $url
	 * @param int    $code
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function redirect($url, $code = self::S302_FOUND): void
	{
		$this->setCode($code);
		$this->setHeader('Location', $url);
	}

	/**
	 * @param string|int|DateTime $time
	 */
	public function setExpiration($time): self
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

	/**
	 * @param string $name
	 * @param string|null $default
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getHeader($name, $default = null): ?string
	{
		return $this->headers[$name] ?? $default;
	}

	/**
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * @param string              $name
	 * @param string              $value
	 * @param string|int|DateTime $time
	 * @param string|null         $path
	 * @param string|null         $domain
	 * @param bool|null           $secure
	 * @param bool|null           $httpOnly
	 * @param string|null         $sameSite
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setCookie($name, $value, $time, $path = null, $domain = null, $secure = null, $httpOnly = null, $sameSite = null): self
	{
		return $this;
	}

	/**
	 * @param string      $name
	 * @param string|null $path
	 * @param string|null $domain
	 * @param bool|null   $secure
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function deleteCookie($name, $path = null, $domain = null, $secure = null): void
	{
	}

}
