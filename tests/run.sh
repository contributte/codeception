./vendor/bin/phpcs -p --standard=vendor/arachne/coding-style/ruleset.xml src
./vendor/bin/phpcs -p --standard=vendor/arachne/coding-style/ruleset.xml --ignore=_* tests
./vendor/bin/codecept build
./vendor/bin/codecept run integration
