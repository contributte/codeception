<?php

namespace Codeception\Module;

use Nette\Utils\Validators;

class Nette extends \Codeception\Util\Framework
{

	/** @var \Nette\DI\Container */
	protected $container;

	/**
	 * @var array $config
	 */
	public function __construct($config = array())
	{
		$this->requiredFields = array('tempDir');
		$this->defaultConfig = array(
			'configFiles' => array(),
			'robotLoader' => array(),
		);
		$this->_reconfigure($config);
	}

	protected function validateConfig()
	{
		parent::validateConfig();
		Validators::assertField($this->config, 'tempDir', 'string');
		Validators::assertField($this->config, 'configFiles', 'array');
		Validators::assertField($this->config, 'robotLoader', 'array');
	}

	public function _initialize()
	{
		self::purge($this->config['tempDir']);
		$configurator = new \Nette\Config\Configurator();
		$configurator->setTempDirectory($this->config['tempDir']);
		foreach ($this->config['configFiles'] as $file) {
			$configurator->addConfig($file);
		}
		$loader = $configurator->createRobotLoader();
		foreach ($this->config['robotLoader'] as $dir) {
			$loader->addDirectory($dir);
		}
		$loader->register();
		$this->container = $configurator->createContainer();
	}

	/**
	 * @param string $service
	 * @return object
	 */
	public function grabService($service)
	{
		try {
			return $this->container->getByType($service);
		} catch (\Nette\DI\MissingServiceException $e) {
			$this->fail($e->getMessage());
		}
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
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
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
