./vendor/bin/phpcs -p --standard=vendor/arachne/coding-style/ruleset.xml --ignore=_* src tests
./vendor/bin/codecept build
./vendor/bin/codecept run --coverage-html
