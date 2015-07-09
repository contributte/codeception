<?php

namespace Arachne\Codeception\DI;

use Nette\DI\CompilerExtension;
use Nette\Http\IResponse;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class CodeceptionExtension extends CompilerExtension
{

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$request = $builder->getByType('Nette\Http\IRequest') ?: 'httpRequest';
		if ($builder->hasDefinition($request)) {
			$builder->getDefinition($request)
				->setClass('Nette\Http\Request')
				->setFactory('Arachne\Codeception\Http\Request');
		}

		$response = $builder->getByType('Nette\Http\IResponse') ?: 'httpResponse';
		if ($builder->hasDefinition($response)) {
			$builder->getDefinition($response)
				->setClass('Nette\Http\IResponse')
				->setFactory('Arachne\Codeception\Http\Response');
		}
	}

}
