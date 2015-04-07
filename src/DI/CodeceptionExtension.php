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
				// RequestFactory leaves port NULL in CLI mode but the urls created by amOnPage have port 80 which breaks canonicalization.
				->addSetup('$url = ?->getUrl(); if (!$url->getPort()) { $url->setPort(80); }', [ '@self' ]);
		}

		$response = $builder->getByType('Nette\Http\IResponse') ?: 'httpResponse';
		if ($builder->hasDefinition($response)) {
			$builder->getDefinition($response)
				->setClass('Nette\Http\IResponse')
				->setFactory('Arachne\Codeception\Http\Response')
				// The HTTP code from previous test sometimes survives in http_response_code() so it's necessary to reset it manually.
				->addSetup('setCode', [ IResponse::S200_OK ]);
		}
	}

}
