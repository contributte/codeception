.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa: phpstan cs

cs:
ifdef GITHUB_ACTION
	vendor/bin/codesniffer -q --report=checkstyle src tests  | cs2pr
else
	vendor/bin/codesniffer src tests
endif

csf:
	vendor/bin/codefixer src tests

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon src

tests:
	vendor/bin/codecept build
	vendor/bin/codecept run --debug

coverage-clover:
	vendor/bin/codecept build
	phpdbg -qrr vendor/bin/codecept run --coverage-xml

coverage-html:
	vendor/bin/codecept build
	phpdbg -qrr vendor/bin/codecept run --coverage-html
