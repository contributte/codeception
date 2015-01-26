<?php

namespace Arachne\Codeception\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Codeception run command input for Symfony console
 *
 * This should only be used when debugging using xDebug and NetBeans (or different IDE).
 *
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RunTestInput extends ArgvInput
{

	public function __construct(InputDefinition $definition = NULL)
	{
		$parameters = [ $_SERVER['argv'][0], 'run' ];

		if (isset($_SERVER['argv'][1])) {
			$filename = $this->normalizePath($_SERVER['argv'][1]);
			$cwd = $this->normalizePath(getcwd()) . '/';

			// IDE always provides absolute path but Codeception only accepts relative path without leading "./".
			// If path is not absolute, make it that way and call realpath to remove "./".
			if (strpos($filename, $cwd) !== 0 && file_exists($cwd . $filename)) {
				$filename = $this->normalizePath(realpath($cwd . $filename));
			}

			if (!file_exists($filename)) {
				echo 'File "' . $filename . '" could not be found.';
				exit;
			}

			// Cut of the absolute part for Codeception.
			$parameters[] = substr($filename, strlen($cwd));
		}

		parent::__construct($parameters, $definition);
	}

	private function normalizePath($path)
	{
		return str_replace('\\', '/', $path);
	}

}
