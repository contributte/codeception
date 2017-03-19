<?php

declare(strict_types=1);

namespace Arachne\Codeception\DI;

use Arachne\Codeception\Http\Request;
use Arachne\Codeception\Http\Response;
use Nette\DI\CompilerExtension;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class HttpExtension extends CompilerExtension
{
    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        $request = $builder->getByType(IRequest::class) ?: 'httpRequest';
        if ($builder->hasDefinition($request)) {
            $builder->getDefinition($request)
                ->setClass(IRequest::class)
                ->setFactory(Request::class);
        }

        $response = $builder->getByType(IResponse::class) ?: 'httpResponse';
        if ($builder->hasDefinition($response)) {
            $builder->getDefinition($response)
                ->setClass(IResponse::class)
                ->setFactory(Response::class);
        }
    }
}
