<?php

namespace Tests\Integration;

use Codeception\Test\Unit;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class DITest extends Unit
{
    protected $tester;

    public function testContainer()
    {
        $this->assertInstanceOf(Container::class, $this->tester->grabService(Container::class));
    }
}
