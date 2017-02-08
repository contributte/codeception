<?php

declare(strict_types=1);

namespace Tests\Functional\Fixtures;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Object;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RouterFactory extends Object
{
    /**
     * @return IRouter
     */
    public function create()
    {
        $router = new RouteList();
        $router[] = new Route('<presenter>[/<action>[/<id>]]', 'Homepage:default');

        return $router;
    }
}
