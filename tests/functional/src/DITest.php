<?php declare(strict_types = 1);

namespace Tests\Functional;

use Codeception\Test\Unit;
use Contributte\Codeception\Module\NetteDIModule;
use Nette\DI\Container;

class DITest extends Unit
{

	/** @var NetteDIModule */
	protected $tester;

	public function testContainer(): void
	{
		$this->assertInstanceOf(Container::class, $this->tester->grabService(Container::class));
	}

}
