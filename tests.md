How to run tests
====

```
# install php-cs-fixer and phpstan
composer global require friendsofphp/php-cs-fixer
composer global require phpstan/phpstan

# go to the project's root directory, but NOT the tests subdirectory 
cd <project_dir>

# install dependencies
composer update

# check coding style
php-cs-fixer fix --dry-run

# fix coding style
php-cs-fixer fix

# static analysis
phpstan analyse --level=4 --configuration=phpstan.neon src tests

# run tests
sh ./tests/run.sh
```

Advanced usage
----

You can use these commands to do more specific tasks.

```
# generate necessary files to run the tests
./vendor/bin/codecept build

# run all tests
./vendor/bin/codecept run

# run the specific suite
./vendor/bin/codecept run <suite>

# run specific test
./vendor/bin/codecept run <file>
```
