<?php declare(strict_types = 1);

namespace Contributte\Codeception\Module;

use Codeception\Module;
use Codeception\TestInterface;
use Nette\Caching\Storages\IJournal;
use Nette\Caching\Storages\SQLiteJournal;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\MissingServiceException;
use Nette\Http\Session;
use Nette\Utils\FileSystem;
use ReflectionProperty;

class NetteDIModule extends Module
{

	/** @var callable[] function(Container $configurator): void; */
	public $onCreateConfigurator = [];

	/** @var callable[] function(Container $container): void; */
	public $onCreateContainer = [];

	/** @var mixed[] */
	protected $config = [
		'configFiles' => [],
		'appDir' => null,
		'logDir' => null,
		'wwwDir' => null,
		'debugMode' => null,
		'removeDefaultExtensions' => false,
		'newContainerForEachTest' => false,
	];

	/** @var string[] */
	protected $requiredFields = [
		'tempDir',
	];

	/** @var string */
	private $path;

	/** @var string[] */
	private $configFiles = [];

	/** @var Container|null */
	private $container;

	/**
	 * @param mixed[] $settings
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function _beforeSuite($settings = []): void
	{
		$this->path = rtrim($settings['path'], '/');
		$this->clearTempDir();
	}

	public function _before(TestInterface $test): void
	{
		if ($this->config['newContainerForEachTest'] === true) {
			$this->clearTempDir();
			$this->container = null;
			$this->configFiles = [];
		}
	}

	public function _afterSuite(): void
	{
		$this->stopContainer();
	}

	public function _after(TestInterface $test): void
	{
		if ($this->config['newContainerForEachTest'] === true) {
			$this->stopContainer();
		}
	}

	/**
	 * @param string[] $configFiles
	 */
	public function useConfigFiles(array $configFiles): void
	{
		if ($this->config['newContainerForEachTest'] !== true) {
			$this->fail('The useConfigFiles can only be used if the newContainerForEachTest option is set to true.');
		}

		if ($this->container !== null) {
			$this->fail('Can\'t set configFiles after the container is created.');
		}

		$this->configFiles = $configFiles;
	}

	public function getContainer(): Container
	{
		if ($this->container === null) {
			$this->createContainer();
		}

		return $this->container;
	}

	/**
	 * @return object
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function grabService(string $service)
	{
		try {
			return $this->getContainer()->getByType($service);
		} catch (MissingServiceException $e) {
			$this->fail($e->getMessage());
		}
	}

	private function createContainer(): void
	{
		$configurator = new Configurator();
		if ($this->config['removeDefaultExtensions'] === true) {
			$configurator->defaultExtensions = [
				'extensions' => ExtensionsExtension::class,
			];
		}

		if ($this->config['logDir'] !== null) {
			$logDir = $this->path . '/' . $this->config['logDir'];
			FileSystem::createDir($logDir);
			$configurator->enableDebugger($logDir);
		}

		$configurator->addParameters([
			'appDir' => $this->path . ($this->config['appDir'] !== null ? '/' . $this->config['appDir'] : ''),
			'wwwDir' => $this->path . ($this->config['wwwDir'] !== null ? '/' . $this->config['wwwDir'] : ''),
		]);

		$this->clearTempDir();
		$tempDir = $this->getTempDir();
		$configurator->setTempDirectory($tempDir);

		if ($this->config['debugMode'] !== null) {
			$configurator->setDebugMode($this->config['debugMode']);
		}

		$configFiles = $this->configFiles !== [] ? $this->configFiles : $this->config['configFiles'];
		foreach ($configFiles as $file) {
			$configurator->addConfig(FileSystem::isAbsolute($file) ? $file : $this->path . '/' . $file);
		}

		foreach ($this->onCreateConfigurator as $callback) {
			$callback($configurator);
		}

		$this->container = $configurator->createContainer();

		foreach ($this->onCreateContainer as $callback) {
			$callback($this->container);
		}
	}


	private function getTempDir(): string
	{
		return $this->path . '/' . $this->config['tempDir'];
	}


	private function clearTempDir(): void
	{
		$this->deleteTempDir();

		$tempDir = $this->getTempDir();
		FileSystem::createDir($tempDir);
	}


	private function deleteTempDir(): void
	{
		$tempDir = $this->getTempDir();
		if (is_dir($tempDir)) {
			FileSystem::delete(realpath($tempDir));
		}
	}


	private function stopContainer(): void
	{
		if ($this->container === null) {
			return;
		}

		try {
			$this->container->getByType(Session::class)->close();
		} catch (MissingServiceException $e) {
			// Session is optional
		}

		try {
			$journal = $this->container->getByType(IJournal::class);
			if ($journal instanceof SQLiteJournal) {
				$property = new ReflectionProperty(SQLiteJournal::class, 'pdo');
				$property->setAccessible(true);
				$property->setValue($journal, null);
			}
		} catch (MissingServiceException $e) {
			// IJournal is optional
		}

		$this->deleteTempDir();
	}

}
