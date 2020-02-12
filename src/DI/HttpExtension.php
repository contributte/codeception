<?php declare(strict_types = 1);

namespace Contributte\Codeception\DI;

use Contributte\Codeception\Http\Request;
use Contributte\Codeception\Http\Response;
use Nette\DI\CompilerExtension;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class HttpExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$request = $builder->getByType(IRequest::class) ?? 'httpRequest';
		if ($builder->hasDefinition($request)) {
			$builder->getDefinition($request)
				->setType(IRequest::class)
				->setFactory(Request::class);
		}

		$response = $builder->getByType(IResponse::class) ?? 'httpResponse';
		if ($builder->hasDefinition($response)) {
			$builder->getDefinition($response)
				->setType(IResponse::class)
				->setFactory(Response::class);
		}
	}

}
