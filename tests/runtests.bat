@ECHO OFF

call "vendor/bin/phpcs.bat" -p --standard=vendor/arachne/coding-style/ruleset.xml src
