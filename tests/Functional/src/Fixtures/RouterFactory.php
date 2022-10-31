<?php declare(strict_types = 1);

namespace Tests\Functional\Fixtures;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{

	public function create(): RouteList
	{
		$router = new RouteList();
		$router[] = new Route('<presenter>[/<action>[/<id>]]', 'Homepage:default');

		return $router;
	}

}
