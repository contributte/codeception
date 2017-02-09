<?php

declare(strict_types=1);

namespace Arachne\Codeception\Http;

use Nette\Http\IRequest;
use Nette\Http\Request as HttpRequest;
use Nette\Http\RequestFactory;
use Nette\Http\UrlScript;

/**
 * HttpRequest class for tests.
 *
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Request extends HttpRequest implements IRequest
{
    /**
     * @var RequestFactory
     */
    private $factory;

    /**
     * @var HttpRequest
     */
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

    public function getCookie(string $key)
    {
        return $this->request->getCookie($key);
    }

    public function getCookies(): array
    {
        return $this->request->getCookies();
    }

    public function getFile(string $key)
    {
        return $this->request->getFile($key);
    }

    public function getFiles(): array
    {
        return $this->request->getFiles();
    }

    public function getHeader(string $header): ?string
    {
        return $this->request->getHeader($header);
    }

    public function getHeaders(): array
    {
        return $this->request->getHeaders();
    }

    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    public function getPost(string $key = null)
    {
        return $this->request->getPost(...func_get_args());
    }

    public function getQuery(string $key = null)
    {
        return $this->request->getQuery(...func_get_args());
    }

    public function getRawBody(): string
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
}
