<?php

namespace Codeception\Module;

use Arachne\Codeception\Util\Connector\Nette as NetteConnector;
use Codeception\TestCase;
use Codeception\Util\Framework;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidStateException;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Validators;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @author Jáchym Toušek
 */
class Nette extends Framework
{

	/** @var Configurator */
	protected $configurator;

	/** @var Container */
	protected $container;

	/** @var string */
	private $suite;

	/**
	 * @var array $config
	 */
	public function __construct($config = array())
	{
		$this->config = array(
			'configFiles' => array(),
		);
		parent::__construct($config);
	}

	protected function validateConfig()
	{
		parent::validateConfig();
		Validators::assertField($this->config, 'configFiles', 'array');
	}

	// TODO: separate Tracy module (exceptions logging)
	// TODO: separate ArachneTools module (debugContent method)
	public function _beforeSuite($settings = array())
	{
		parent::_beforeSuite($settings);

		$this->detectSuiteName($settings);
		$path = pathinfo($settings['path'], PATHINFO_DIRNAME);
		$tempDir = $path . DIRECTORY_SEPARATOR . '_temp' . DIRECTORY_SEPARATOR . $this->suite;
		Debugger::$logDirectory = $path . DIRECTORY_SEPARATOR . '_log';

		self::purge($tempDir);
		$this->configurator = new Configurator();
		$this->configurator->setDebugMode(FALSE);
		$this->configurator->setTempDirectory($tempDir);
		$this->configurator->addParameters(array(
			'container' => array(
				'class' => $this->getContainerClass(),
			),
		));

		$files = $this->config['configFiles'];
		$files[] = __DIR__ . '/config.neon';
		foreach ($files as $file) {
			$this->configurator->addConfig($file);
		}

		// Generates and loads the container class.
		// The actual container is created later.
		$this->configurator->createContainer();
	}

	public function _before(TestCase $test)
	{
		$class = $this->getContainerClass();
		// Cannot use $this->configurator->createContainer() directly beacuse it would call $container->initialize().
		// Container initialization is called laiter by NetteConnector.
		$this->container = new $class;
		$this->client = new NetteConnector();
		$this->client->setContainer($this->container);
		parent::_before($test);
	}

	public function _after(TestCase $test)
	{
		parent::_after($test);
		$_SESSION = array();
		$_GET = array();
		$_POST = array();
		$_COOKIE = array();
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

	public function debugContent()
	{
		$this->debugSection('Content', $this->client->getInternalResponse()->getContent());
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
		$this->suite = substr($directory, $position + 1);
	}

	private function getContainerClass()
	{
		return ucfirst($this->suite) . 'SuiteContainer';
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
