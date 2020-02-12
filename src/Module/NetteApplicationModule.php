<?php declare(strict_types = 1);

namespace Contributte\Codeception\Module;

use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Contributte\Codeception\Connector\NetteConnector;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class NetteApplicationModule extends Framework
{

	/** @var NetteConnector */
	public $client;

	/** @var mixed[] */
	protected $config = [
		'followRedirects' => true,
	];

	/** @var string */
	private $path;

	/**
	 * @param mixed[] $settings
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function _beforeSuite($settings = []): void
	{
		$this->path = $settings['path'];
	}

	public function _before(TestInterface $test): void
	{
		$this->client = new NetteConnector();
		$this->client->setContainerAccessor(
			function (): Container {
				/** @var NetteDIModule $diModule */
				$diModule = $this->getModule(NetteDIModule::class);

				return $diModule->getContainer();
			}
		);
		$this->client->followRedirects($this->config['followRedirects']);

		parent::_before($test);
	}

	public function _after(TestInterface $test): void
	{
		parent::_after($test);

		$_SESSION = [];
		$_GET = [];
		$_POST = [];
		$_FILES = [];
		$_COOKIE = [];
	}

	public function followRedirects(bool $followRedirects): void
	{
		$this->client->followRedirects($followRedirects);
	}

	public function seeRedirectTo(string $url): void
	{
		if ($this->client->isFollowingRedirects()) {
			$this->fail('Method seeRedirectTo only works when followRedirects option is disabled');
		}

		/** @var NetteDIModule $diModule */
		$diModule = $this->getModule(NetteDIModule::class);
		$request = $diModule->grabService(IRequest::class);
		$response = $diModule->grabService(IResponse::class);
		if ($response->getHeader('Location') !== $request->getUrl()->getHostUrl() . $url && $response->getHeader('Location') !== $url) {
			$this->fail('Couldn\'t confirm redirect target to be "' . $url . '", Location header contains "' . $response->getHeader('Location') . '".');
		}
	}

	public function debugContent(): void
	{
		$this->debugSection('Content', $this->client->getInternalResponse()->getContent());
	}

}
