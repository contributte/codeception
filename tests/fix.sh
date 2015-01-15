cd ./vendor/bin 
./phpcbf --standard=../arachne/coding-style/ruleset.xml ../../src
./phpcbf --standard=../arachne/coding-style/ruleset.xml --ignore=_* ../../tests
