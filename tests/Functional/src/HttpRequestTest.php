<?php declare(strict_types = 1);

namespace Functional\src;

use Codeception\Test\Unit;
use Contributte\Codeception\Http\Request;
use Contributte\Codeception\Module\NetteApplicationModule;
use Contributte\Codeception\Module\NetteDIModule;
use Nette\Http\IRequest;

class HttpRequestTest extends Unit
{

	/** @var NetteApplicationModule|NetteDIModule */
	protected $tester;

	public function testHttpRequest(): void
	{
		$this->tester->amHttpAuthenticated('username', 'password');

		$this->tester->amOnPage('/article/page');

		$httpRequest = $this->tester->grabService(IRequest::class);

		$this->assertInstanceOf(Request::class, $httpRequest);

		$this->assertSame(['username', 'password'], $httpRequest->getBasicCredentials());
	}

}
