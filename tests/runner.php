<?php declare(strict_types = 1);

use Codeception\Codecept;
use Codeception\Command\Run;
use Contributte\Codeception\Console\RunTestInput;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application('Codeception', Codecept::VERSION);
$app->add(new Run('run'));

$app->run(new RunTestInput());
