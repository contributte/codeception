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

    public function reset(): void
    {
        $this->code = self::S200_OK;
        $this->headers = [];
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function setContentType(string $type, ?string $charset = null): self
    {
        $this->setHeader('Content-Type', $type.($charset ? '; charset='.$charset : ''));

        return $this;
    }

    public function redirect(string $url, int $code = self::S302_FOUND): void
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

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string|int|DateTime $time
     */
    public function setCookie(string $name, string $value, $time, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httpOnly = null, ?string $sameSite = null): self
    {
        return $this;
    }

    public function deleteCookie(string $name, ?string $path = null, ?string $domain = null, ?bool $secure = null): void
    {
    }
}
