<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Test\Unit;
use Nette\Application\Application;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ApplicationTest extends Unit
{
    protected $tester;

    public function testApplication()
    {
        $this->assertInstanceOf(Application::class, $this->tester->grabService(Application::class));
    }

    public function testPage()
    {
        $this->tester->amOnPage('/article/page');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('headline', 'h1');
    }

    public function testLink()
    {
        $this->tester->amOnPage('/article/link');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Normal link');
        $this->tester->seeLink('Normal link', '/article/page');
    }

    public function testRedirect()
    {
        $this->tester->amOnPage('/article/redirect');
        $this->tester->seeResponseCodeIs(301);
        $this->tester->seeRedirectTo('/article/page');
    }

    public function testUnknown()
    {
        $this->tester->amOnPage('/article/unknown');
        $this->tester->seeResponseCodeIs(404);
    }
}
