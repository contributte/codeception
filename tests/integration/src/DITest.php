<?php

namespace Tests\Integration;

use Codeception\TestCase\Test;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class DITest extends Test
{
    public function testContainer()
    {
        $this->assertInstanceOf(Container::class, $this->guy->grabService(Container::class));
    }
}
