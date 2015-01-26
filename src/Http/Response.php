<?php

namespace Arachne\Codeception\Http;

use Nette\Http\IResponse;
use Nette\Http\Response as HttpResponse;
use Nette\Object;
use Nette\Utils\DateTime;

/**
 * HttpResponse class for tests.
 *
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Response extends Object implements IResponse
{

	/** @var int */
	private $code = self::S200_OK;

	/** @var array */
	private $headers = [];

	/**
	 * @param int
	 * @return self
	 */
	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return self
	 */
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return self
	 */
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * @param string $type
	 * @param string $charset
	 * @return self
	 */
	public function setContentType($type, $charset = NULL)
	{
		$this->setHeader('Content-Type', $type . ($charset ? '; charset=' . $charset : ''));
		return $this;
	}

	/**
	 * @param string $url
	 * @param int $code
	 */
	public function redirect($url, $code = self::S302_FOUND)
	{
		$this->setCode($code);
		$this->setHeader('Location', $url);
	}

	/**
	 * @param string|int|DateTime $time
	 * @return self
	 */
	public function setExpiration($time)
	{
		if (!$time) {
			$this->setHeader('Cache-Control', 's-maxage=0, max-age=0, must-revalidate');
			$this->setHeader('Expires', 'Mon, 23 Jan 1978 10:00:00 GMT');
			return $this;
		}

		$time = DateTime::from($time);
		$this->setHeader('Cache-Control', 'max-age=' . ($time->format('U') - time()));
		$this->setHeader('Expires', HttpResponse::date($time));
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSent()
	{
		return FALSE;
	}

	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getHeader($name, $default = NULL)
	{
		return isset($this->headers[$name]) ? $this->headers[$name] : $default;
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string|int|DateTime $time
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httpOnly
	 * @return self
	 */
	public function setCookie($name, $value, $time, $path = NULL, $domain = NULL, $secure = NULL, $httpOnly = NULL)
	{
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 */
	public function deleteCookie($name, $path = NULL, $domain = NULL, $secure = NULL)
	{
	}

	public function removeDuplicateCookies()
	{
	}

}
