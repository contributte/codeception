<?php

namespace Codeception\Module;

use Arachne\Codeception\ConfigFilesInterface;
use Arachne\Codeception\Connector\Nette as NetteConnector;
use Arachne\Codeception\DI\CodeceptionExtension;
use Codeception\TestCase;
use Codeception\Lib\Framework;
use Arachne\Bootstrap\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\InvalidStateException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Nette extends Framework
{

	/** @var Configurator */
	protected $configurator;

	/** @var Container */
	protected $container;

	/** @var string */
	private $suite;

	/** @var string */
	private $path;

	// TODO: separate ArachneTools module (debugContent method)
	public function _beforeSuite($settings = [])
	{
		parent::_beforeSuite($settings);

		$this->detectSuiteName($settings);
		$this->path = pathinfo($settings['path'], PATHINFO_DIRNAME);

		self::purge($this->path . DIRECTORY_SEPARATOR . '_temp' . DIRECTORY_SEPARATOR . $this->suite);
	}

	public function _before(TestCase $test)
	{
		$tempDir = $this->path . DIRECTORY_SEPARATOR . '_temp' . DIRECTORY_SEPARATOR . $this->suite . DIRECTORY_SEPARATOR . (new \ReflectionClass($test))->getShortName() . '_' . $test->getName();
		@mkdir($tempDir, 0777, TRUE);

		$this->configurator = new Configurator();
		$this->configurator->setDebugMode(FALSE);
		$this->configurator->setTempDirectory($tempDir);
		if (!class_exists('Nette\DI\ContainerLoader')) { // Nette 2.2 compatibility
			$this->configurator->addParameters([
				'container' => [
					'class' => $this->getContainerClass(),
				],
			]);
		}
		$this->configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('arachne.codeception', new CodeceptionExtension());
		};

		if ($test instanceof ConfigFilesInterface) {
			foreach ($test->getConfigFiles() as $file) {
				$this->configurator->addConfig($this->path . DIRECTORY_SEPARATOR . $this->suite . DIRECTORY_SEPARATOR . $file);
			}
		}

		// Generates and loads the container class.
		// The actual container is created later.
		$class = get_class($this->configurator->createContainer());

		// Cannot use $this->configurator->createContainer() directly beacuse it would call $container->initialize().
		// Container initialization is called laiter by NetteConnector.
		$this->container = new $class;
		$this->client = new NetteConnector();
		$this->client->setContainer($this->container);
		// TODO: make this configurable
		$this->client->followRedirects(FALSE);
		parent::_before($test);
	}

	public function _after(TestCase $test)
	{
		parent::_after($test);
		$_SESSION = [];
		$_GET = [];
		$_POST = [];
		$_COOKIE = [];
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

	public function seeRedirectTo($url)
	{
		$request = $this->container->getByType('Nette\Http\IRequest');
		$response = $this->container->getByType('Nette\Http\IResponse');
		if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl() . $url && $response->getHeader('Location') !== $url) {
			$this->fail('Couldn\'t confirm redirect target to be "' . $url . '", Location header contains "' . $response->getHeader('Location') . '".');
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
			return;
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
