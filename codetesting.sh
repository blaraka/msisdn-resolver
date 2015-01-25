!/bin/bash
php -l src/*
php -l tests/*
vendor/squizlabs/php_codesniffer/scripts/phpcs --standard=PSR2 src/ tests/
vendor/sebastian/phpcpd/phpcpd --min-lines 3 --min-tokens 50 src/ tests/
vendor/phpmd/phpmd/src/bin/phpmd src/ text codesize,design,naming,unusedcode,controversial --strict
vendor/phpmd/phpmd/src/bin/phpmd tests/ text codesize,design,naming,unusedcode,controversial --strict