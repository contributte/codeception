<?php

declare(strict_types=1);

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

    public function __construct($config, $options)
    {
        parent::__construct($config, $options);
        Debugger::$logDirectory = $this->getLogDir();
    }

    public function testFail(FailEvent $event): void
    {
        Debugger::log($event->getFail());
    }

    public function testError(FailEvent $event): void
    {
        Debugger::log($event->getFail());
    }
}
