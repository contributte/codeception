<?php

namespace Arachne\Codeception\Module;

use Codeception\Module;
use Codeception\TestCase;
use Nette\Configurator;
use Nette\Utils\FileSystem;

class Container extends Module
{
    protected $config = [
        'configFiles' => [],
        'logDir' => null,
        'debugMode' => null,
        'configurator' => Configurator::class,
    ];

    protected $requiredFields = [
        'tempDir',
    ];

    /**
     * @var string
     */
    private $path;

    public function _beforeSuite($settings = [])
    {
        $this->path = $settings['path'];
    }

    public function _before(TestCase $test)
    {
        $tempDir = $this->path.'/'.$this->config['tempDir'];
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
    }

    public function _afterSuite()
    {
        FileSystem::delete($this->path.'/'.$this->config['tempDir']);
    }

    public function createContainer(array $configFiles = null)
    {
        $configurator = new $this->config['configurator']();

        if ($this->config['logDir']) {
            $configurator->enableDebugger($this->path.'/'.$this->config['logDir']);
        }

        $tempDir = $this->path.'/'.$this->config['tempDir'];
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        $configurator->setTempDirectory($tempDir);

        if ($this->config['debugMode'] !== null) {
            $configurator->setDebugMode($this->config['debugMode']);
        }

        $configFiles = is_array($configFiles) ? $configFiles : $this->config['configFiles'];
        foreach ($configFiles as $file) {
            $configurator->addConfig($this->path.'/'.$file, false);
        }

        return $configurator->createContainer();
    }
}
