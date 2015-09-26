How to run tests
====

```
# go to the project's root directory, but NOT the tests subdirectory 
cd <project_dir>

# install dependencies
composer update

# run the coding style checker and all tests
sh ./tests/run.sh

# fix coding style problems automatically
sh ./tests/fix.sh
```

Advanced usage
----

You can use these commands to do more specific tasks.

```
# generate necessary files to run the tests
./vendor/bin/codecept build

# run the unit suite
./vendor/bin/codecept run unit

# run the integration suite
./vendor/bin/codecept run integration

# run specific test
./vendor/bin/codecept run tests/unit/src/FooTest.php
```

Testing with Nette 2.2
----

If you want to run the tests with Nette 2.2 use these commands to install the dependencies. Then run the tests normally.

```
# tell composer to use different json file
set COMPOSER=composer-nette_2.2.json

# install dependencies
composer update

# reset the environment variable to normal
set COMPOSER=composer.json
```
