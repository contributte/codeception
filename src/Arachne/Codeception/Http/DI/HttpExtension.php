<?php

namespace Arachne\Codeception\Http\DI;

use Nette\DI\CompilerExtension;

/**
 * @author Jáchym Toušek
 */
class HttpExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('httpResponse')
			->setClass('Arachne\Codeception\Http\Response');
	}

}
