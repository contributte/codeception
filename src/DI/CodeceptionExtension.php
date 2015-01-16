<?php

namespace Arachne\Codeception\DI;

use Nette\Bridges\Framework\NetteExtension;
use Nette\DI\CompilerExtension;
use Nette\Http\IResponse;

/**
 * @author Jáchym Toušek
 */
class CodeceptionExtension extends CompilerExtension
{

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		if ($builder->hasDefinition('httpResponse')) {
			$builder->getDefinition('httpResponse')
				->setClass('Arachne\Codeception\Http\Response')
				// The HTTP code from previous test sometimes survives in http_response_code() so it's necessary to reset it manually.
				->addSetup('setCode', [ IResponse::S200_OK]);
		}

		if ($builder->hasDefinition('httpRequest')) {
			$builder->getDefinition('httpRequest')
				// RequestFactory leaves port NULL in CLI mode but the urls created by amOnPage have port 80 which breaks canonicalization.
				->addSetup('$url = ?->getUrl(); if (!$url->getPort()) { $url->setPort(80); }', [ '@self' ]);
		}
	}

}
