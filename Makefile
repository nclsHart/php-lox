default: install

install:
	@composer install

test:
	@vendor/bin/phpunit --colors

cs:
	@vendor/bin/php-cs-fixer fix

.PHONY: install test cs
