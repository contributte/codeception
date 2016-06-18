<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Tracy;

use Codeception\Event\FailEvent;
use Codeception\Events;
use Codeception\Extension;
use Tracy\Debugger;

class Logger extends Extension
{
    public static $events = [
        Events::TEST_FAIL => 'testFail',
        Events::TEST_ERROR => 'testError',
    ];

    public function __construct()
    {
        Debugger::$logDirectory = $this->getLogDir();
    }

    public function testFail(FailEvent $e)
    {
        Debugger::log($e->getFail());
    }

    public function testError(FailEvent $e)
    {
        Debugger::log($e->getFail());
    }
}
