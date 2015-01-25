#!/bin/bash

echo -e "\n1. Running php -l src/*"
php -l src/*
echo -e "\n2. Running php -l tests/*\n"
php -l tests/*
echo -e "\n3. Running phpcs --standard=PSR2\n"
vendor/squizlabs/php_codesniffer/scripts/phpcs --standard=PSR2 src/ tests/
echo -e "\n4. Running phpcpd --min-lines 3 --min-tokens 50\n"
vendor/sebastian/phpcpd/phpcpd --min-lines 3 --min-tokens 50 src/ tests/
echo -e "\n\n5. Running nphpmd src/ text codesize,design,naming,unusedcode,controversial --strict"
vendor/phpmd/phpmd/src/bin/phpmd src/ text codesize,design,naming,unusedcode,controversial --strict
echo -e "\n6. Running phpmd tests/ text codesize,design,naming,unusedcode,controversial --strict"
vendor/phpmd/phpmd/src/bin/phpmd tests/ text codesize,design,naming,unusedcode,controversial --strict
echo -e "\n"