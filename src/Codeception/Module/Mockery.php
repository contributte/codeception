<?php

namespace Codeception\Module;

use Codeception\Module;
use Codeception\TestCase;

/**
 * @author Jáchym Toušek
 */
class Mockery extends Module
{

	public function _after(TestCase $test)
	{
		\Mockery::close();
	}

}
