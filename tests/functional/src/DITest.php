<?php

declare(strict_types=1);

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteDIModule;
use Codeception\Test\Unit;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class DITest extends Unit
{
    /**
     * @var NetteDIModule
     */
    protected $tester;

    public function testContainer()
    {
        $this->assertInstanceOf(Container::class, $this->tester->grabService(Container::class));
    }
}
