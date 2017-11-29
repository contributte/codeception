<?php

declare(strict_types=1);

namespace Arachne\Codeception\Http;

use Nette\Http\IResponse;
use Nette\Http\Response as HttpResponse;
use Nette\Utils\DateTime;

/**
 * HttpResponse class for tests.
 *
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Response implements IResponse
{
    /**
     * @var int
     */
    private $code = self::S200_OK;

    /**
     * @var array
     */
    private $headers = [];

    public function reset(): void
    {
        $this->code = self::S200_OK;
        $this->headers = [];
    }

    /**
     * @param int $code
     */
    public function setCode($code): self
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
     */
    public function setHeader($name, $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addHeader($name, $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $type
     * @param string $charset
     */
    public function setContentType($type, $charset = null): self
    {
        $this->setHeader('Content-Type', $type.($charset ? '; charset='.$charset : ''));

        return $this;
    }

    /**
     * @param string $url
     * @param int    $code
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
        $this->setHeader('Cache-Control', 'max-age='.($time->format('U') - time()));
        $this->setHeader('Expires', HttpResponse::date($time));

        return $this;
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return false;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getHeader($name, $default = null)
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
     * @param string              $name
     * @param string              $value
     * @param string|int|DateTime $time
     * @param string              $path
     * @param string              $domain
     * @param bool                $secure
     * @param bool                $httpOnly
     *
     * @return self
     */
    public function setCookie($name, $value, $time, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        return $this;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     */
    public function deleteCookie($name, $path = null, $domain = null, $secure = null)
    {
    }

    public function removeDuplicateCookies()
    {
    }
}
