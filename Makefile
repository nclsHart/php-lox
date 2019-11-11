default: install

install:
	@composer install

test:
	@vendor/bin/phpunit --colors

.PHONY: install test
