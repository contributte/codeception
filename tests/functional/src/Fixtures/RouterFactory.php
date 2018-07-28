<?php declare(strict_types = 1);

namespace Tests\Functional\Fixtures;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{

	public function create(): IRouter
	{
		$router = new RouteList();
		$router[] = new Route('<presenter>[/<action>[/<id>]]', 'Homepage:default');

		return $router;
	}

}
