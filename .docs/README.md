# Codeception

This library is here to help you with Integration and [Functional Tests](http://codeception.com/docs/04-FunctionalTests) for your Nette application.

## Content

- [Setup](#setup)
- [Usage](#usage)
	- [DI module - Nette\DI integration](#nettedimodule)
	- [Application module - Nette\Application integration](#netteapplicationmodule)
- [Development](#development)

## Setup

```bash
composer require contributte/codeception
```

## Usage

### NetteDIModule

When you want to write an integration test to make sure that some services work well together you need to create the DI container first.

```yaml
# /tests/integration.suite.yml
error_level: "E_ALL"
class_name: IntegrationSuiteTester
modules:
    enabled:
        - Contributte\Codeception\Module\NetteDIModule:
            tempDir: ../_temp/integration
            configFiles:
                - config/config.neon
            # Log directory for Tracy.
            # logDir: ../_log
            # Debug mode.
            # debugMode: true
            # Get rid of the default extensions.
            # removeDefaultExtensions: true
            # Compile and create new container for each test.
            # newContainerForEachTest: true
```

```neon
# /tests/integration/config/config.neon
services:
	- MyService
```

```php
# /tests/integration/src/MyServiceTest.php
use Codeception\Test\Unit;

class MyServiceTest extends Unit
{
	public function testMyService(): void
	{
		// Here you can override the configFiles from integration.suite.yml if needed.
		// The newContainerForEachTest option is required for this.
		// $this->tester->useConfigFiles(['config/another-config.neon']);
		$this->assertInstanceOf(MyService::class, $this->tester->grabService(MyService::class));
	}
}
```
`useConfigFiles` method takes array of file paths that are either absolute or relative to suite root.

### NetteApplicationModule

In functional tests you want to emulate the HTTP request and run `Nette\Application\Application` to handle it.

Unfortunately Nette framework has some downsides like the fact that Request and Response are registered as services in the DI Container. For this reason the NetteApplicationModule requires `Contributte\Codeception\DI\CodeceptionExtension` to override the default implementations. **Beware that this is meant for the functional tests only. Do NOT register the extension outside of tests.**

```yaml
# /tests/functional.suite.yml
error_level: "E_ALL"
class_name: FunctionalSuiteTester
modules:
    enabled:
        - Contributte\Codeception\Module\NetteApplicationModule
        - Contributte\Codeception\Module\NetteDIModule:
            tempDir: ../_temp/functional
            configFiles:
                # Your application config file.
                - ../../app/config/config.neon
                # Additional config file only to add Contributte\Codeception\DI\HttpExtension.
                - config/config.neon
```

```neon
# /tests/functional/config/config.neon
extensions:
	codeception: Contributte\Codeception\DI\HttpExtension
```

```php
# /tests/functional/src/HomepageTest.php
use Codeception\Test\Unit;
class HomepageTest extends Unit
{
	public function testHomepage(): void
	{
		// Create http request and run Nette\Application\Application. See Contributte\Codeception\Connector\NetteConnector for details.
		$this->tester->amOnPage('/');
		// Assert that the response is what you expect.
		$this->tester->seeResponseCodeIs(200);
		$this->tester->see('Hello World!', 'h1');
	}
}
```

## Development

Simply run scripts in `Makefile` and make sure that qa, tester and phpstan passed.

### Advanced usage

You can use these commands to do more specific tasks.

```bash
# generate necessary files to run the tests
./vendor/bin/codecept build

# run all tests
./vendor/bin/codecept run

# run the specific suite
./vendor/bin/codecept run <suite>

# run specific test
./vendor/bin/codecept run <file>
```
