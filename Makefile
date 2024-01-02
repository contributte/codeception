.PHONY: install qa cs csf phpstan tests coverage

install:
	composer update

qa: phpstan cs

cs:
ifdef GITHUB_ACTION
	vendor/bin/codesniffer -q --report=checkstyle src tests | cs2pr
else
	vendor/bin/codesniffer src tests
endif

csf:
	vendor/bin/codefixer src tests

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

tests:
	vendor/bin/codecept build
	vendor/bin/codecept run --debug

coverage:
ifdef GITHUB_ACTION
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage-xml
else
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage-html
endif
