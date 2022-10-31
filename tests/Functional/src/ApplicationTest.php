<?php declare(strict_types = 1);

namespace Tests\Functional;

use Codeception\Test\Unit;
use Contributte\Codeception\Module\NetteApplicationModule;
use Contributte\Codeception\Module\NetteDIModule;
use Nette\Application\Application;

class ApplicationTest extends Unit
{

	/** @var NetteApplicationModule|NetteDIModule */
	protected $tester;

	public function testApplication(): void
	{
		$this->assertInstanceOf(Application::class, $this->tester->grabService(Application::class));
	}

	public function testPage(): void
	{
		$this->tester->amOnPage('/article/page');
		$this->tester->seeResponseCodeIs(200);
		$this->tester->see('headline', 'h1');
	}

	public function testLink(): void
	{
		$this->tester->amOnPage('/article/link');
		$this->tester->seeResponseCodeIs(200);
		$this->tester->see('Normal link');
		$this->tester->seeLink('Normal link', '/article/page');
	}

	public function testRedirect(): void
	{
		$this->tester->amOnPage('/article/redirect');
		$this->tester->seeResponseCodeIs(301);
		$this->tester->seeRedirectTo('/article/page');
	}

	public function testUnknown(): void
	{
		$this->tester->amOnPage('/article/unknown');
		$this->tester->seeResponseCodeIs(404);
	}

}
