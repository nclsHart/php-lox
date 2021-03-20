default: install

install:
	@composer install

test:
	@vendor/bin/phpunit --colors

cs:
	@vendor/bin/php-cs-fixer fix

static-analysis:
	@vendor/bin/psalm

.PHONY: install test cs static-analysis
