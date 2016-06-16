<?php

use Arachne\Codeception\Console\RunTestInput;
use Codeception\Codecept;
use Codeception\Command\Run;
use Symfony\Component\Console\Application;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application('Codeception', Codecept::VERSION);
$app->add(new Run('run'));

$app->run(new RunTestInput());
