<?php

namespace Arachne\Codeception\Http;

use Nette\Http\IRequest;
use Nette\Http\Request as HttpRequest;
use Nette\Http\RequestFactory;

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

    public function reset()
    {
        $this->request = $this->factory->createHttpRequest();
        $url = $this->request->getUrl();
        if (!$url->getPort()) {
            $url->setPort(80); // Fix canonicalization in Nette 2.2.
        }
    }

    public function getCookie($key, $default = null)
    {
        return $this->request->getCookie($key, $default);
    }

    public function getCookies()
    {
        return $this->request->getCookies();
    }

    public function getFile($key)
    {
        return call_user_func_array([$this->request, 'getFile'], func_get_args());
    }

    public function getFiles()
    {
        return $this->request->getFiles();
    }

    public function getHeader($header, $default = null)
    {
        return $this->request->getHeader($header, $default);
    }

    public function getHeaders()
    {
        return $this->request->getHeaders();
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function getPost($key = null, $default = null)
    {
        return call_user_func_array([$this->request, 'getPost'], func_get_args());
    }

    public function getQuery($key = null, $default = null)
    {
        return call_user_func_array([$this->request, 'getQuery'], func_get_args());
    }

    public function getRawBody()
    {
        return $this->request->getRawBody();
    }

    public function getRemoteAddress()
    {
        return $this->request->getRemoteAddress();
    }

    public function getRemoteHost()
    {
        return $this->request->getRemoteHost();
    }

    public function getUrl()
    {
        return $this->request->getUrl();
    }

    public function isAjax()
    {
        return $this->request->isAjax();
    }

    public function isMethod($method)
    {
        return $this->request->isMethod($method);
    }

    public function isSecured()
    {
        return $this->request->isSecured();
    }
}
