<?php

namespace Arachne\Codeception\Console\Input;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Codeception run command input for Symfony console
 *
 * This should only be used when debugging using xDebug and NetBeans (or different IDE).
 *
 * @author Jáchym Toušek
 */
class RunTestInput extends ArgvInput
{

	public function __construct(InputDefinition $definition = NULL)
	{
		$parameters = array($_SERVER['argv'][0], 'run');

		if (isset($_SERVER['argv'][1])) {
			$filename = str_replace('\\', '/', $_SERVER['argv'][1]);
			$cwd = str_replace('\\', '/', getcwd()) . '/';
			if (strpos($filename, $cwd) === 0) {
				$parameters[] = substr($filename, strlen($cwd));
			}
		}

		parent::__construct($parameters, $definition);
	}

}
