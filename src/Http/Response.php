<?php

declare(strict_types=1);

namespace Arachne\Codeception\Http;

use Nette\Http\Helpers;
use Nette\Http\IResponse;
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

    public function reset()
    {
        $this->code = self::S200_OK;
        $this->headers = [];
    }

    /**
     * @param int $code
     *
     * @return static
     */
    public function setCode(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function addHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $type
     * @param string $charset
     *
     * @return static
     */
    public function setContentType(string $type, string $charset = null)
    {
        $this->setHeader('Content-Type', $type.($charset ? '; charset='.$charset : ''));

        return $this;
    }

    /**
     * @param string $url
     * @param int    $code
     */
    public function redirect(string $url, int $code = self::S302_FOUND): void
    {
        $this->setCode($code);
        $this->setHeader('Location', $url);
    }

    /**
     * @param string|int|DateTime $time
     *
     * @return static
     */
    public function setExpiration($time)
    {
        if (!$time) {
            $this->setHeader('Cache-Control', 's-maxage=0, max-age=0, must-revalidate');
            $this->setHeader('Expires', 'Mon, 23 Jan 1978 10:00:00 GMT');

            return $this;
        }

        $time = DateTime::from($time);
        $this->setHeader('Cache-Control', 'max-age='.($time->format('U') - time()));
        $this->setHeader('Expires', Helpers::formatDate($time));

        return $this;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return false;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
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
    public function setCookie(string $name, string $value, $time, string $path = null, string $domain = null, bool $secure = null, bool $httpOnly = null, string $sameSite = null)
    {
        return $this;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     */
    public function deleteCookie(string $name, string $path = null, string $domain = null, bool $secure = null): void
    {
    }
}
