<?php

namespace Arachne\Codeception\DI;

use Nette\DI\CompilerExtension;

/**
 * @author Jáchym Toušek
 */
class CodeceptionExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('httpResponse')
			->setClass('Arachne\Codeception\Http\Response');
	}

}
