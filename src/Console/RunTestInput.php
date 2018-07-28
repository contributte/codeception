<?php declare(strict_types = 1);

namespace Contributte\Codeception\Console;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Codeception run command input for Symfony console.
 *
 * This should only be used when debugging using xDebug and NetBeans (or different IDE).
 */
class RunTestInput extends ArgvInput
{

	public function __construct(?InputDefinition $definition = null)
	{
		$parameters = [$_SERVER['argv'][0], 'run'];

		if (isset($_SERVER['argv'][1])) {
			$filename = $this->normalizePath($_SERVER['argv'][1]);
			$cwd = $this->normalizePath((string) getcwd()) . '/';

			// IDE always provides absolute path but Codeception only accepts relative path without leading "./".
			// If path is not absolute, make it that way and call realpath to remove "./".
			if (strpos($filename, $cwd) !== 0 && file_exists($cwd . $filename)) {
				$filename = $this->normalizePath((string) realpath($cwd . $filename));
			}

			if (!file_exists($filename)) {
				echo 'File "' . $filename . '" could not be found.';
				exit;
			}

			// Cut off the absolute part for Codeception.
			$parameters[] = substr($filename, strlen($cwd));
		}

		parent::__construct($parameters, $definition);
	}

	private function normalizePath(string $path): string
	{
		return str_replace('\\', '/', $path);
	}

}
