<?php

namespace Codeception\Module;

use Codeception\Util\Framework;
use Nette\Configurator;
use Nette\InvalidStateException;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Validators;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Nette extends Framework
{

	/** @var Container */
	protected $container;

	/** @var RobotLoader */
	private $robotLoader;

	/**
	 * @var array $config
	 */
	public function __construct($config = array())
	{
		$this->requiredFields = array('tempDir');
		$this->config = array(
			'configFiles' => array(),
			'robotLoader' => array(),
		);
		parent::__construct($config);
	}

	protected function validateConfig()
	{
		parent::validateConfig();
		Validators::assertField($this->config, 'tempDir', 'string');
		Validators::assertField($this->config, 'configFiles', 'array');
		Validators::assertField($this->config, 'robotLoader', 'array');
	}

	public function _beforeSuite($settings = array())
	{
		parent::_beforeSuite($settings);

		$suite = $this->detectSuiteName($settings);
		$tempDir = $this->config['tempDir'] . DIRECTORY_SEPARATOR . $suite;

		self::purge($tempDir);
		$configurator = new Configurator();
		$configurator->setTempDirectory($tempDir);
		$configurator->addParameters(array(
			'container' => array(
				'class' => ucfirst($suite) . 'SuiteContainer',
			),
		));
		$files = $this->config['configFiles'];
		$files[] = __DIR__ . '/config.neon';
		foreach ($files as $file) {
			$configurator->addConfig($file);
		}
		$this->robotLoader = $configurator->createRobotLoader();
		foreach ($this->config['robotLoader'] as $dir) {
			$this->robotLoader->addDirectory($dir);
		}
		$this->robotLoader->register();
		$this->container = $configurator->createContainer();
	}

	public function _afterSuite()
	{
		$this->robotLoader->unregister();
	}

	/**
	 * @param string $service
	 * @return object
	 */
	public function grabService($service)
	{
		try {
			return $this->container->getByType($service);
		} catch (MissingServiceException $e) {
			$this->fail($e->getMessage());
		}
	}

	private function detectSuiteName($settings)
	{
		if (!isset($settings['path'])) {
			throw new InvalidStateException('Could not detect suite name, path is not set.');
		}
		$directory = rtrim($settings['path'], DIRECTORY_SEPARATOR);
		$position = strrpos($directory, DIRECTORY_SEPARATOR);
		if ($position === FALSE) {
			throw new InvalidStateException('Could not detect suite name, path is invalid.');
		}
		return substr($directory, $position + 1);
	}

	/**
	 * Purges directory.
	 * @param string $dir
	 */
	protected static function purge($dir)
	{
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
			if (substr($entry->getBasename(), 0, 1) === '.') {
				// nothing
			} elseif ($entry->isDir()) {
				rmdir($entry);
			} else {
				unlink($entry);
			}
		}
	}

}
