<?php

namespace Tests\Integration;

use Arachne\Codeception\ConfigFilesInterface;
use Codeception\TestCase\Test;
use Nette\Application\Application;

/**
 * @author Jáchym Toušek
 */
class ApplicationTest extends Test implements ConfigFilesInterface
{

	public function getConfigFiles()
	{
		return [
			'config/application.neon',
		];
	}

	public function testApplication()
	{
		$this->assertInstanceOf(Application::class, $this->guy->grabService(Application::class));
	}

	public function testPage()
	{
		$this->guy->amOnPage('/article/page');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('headline', 'h1');
	}

	public function testLink()
	{
		$this->guy->amOnPage('/article/link');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('Normal link');
		$this->guy->seeLink('Normal link', '/article/page');
	}

	public function testRedirect()
	{
		$this->guy->amOnPage('/article/redirect');
		$this->guy->seeRedirectTo('/article/page');
		$this->guy->seeResponseCodeIs(301);
	}

	public function testUnknown()
	{
		$this->guy->amOnPage('/article/unknown');
		$this->guy->seeResponseCodeIs(404);
	}

}
