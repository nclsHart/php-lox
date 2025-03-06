default: help

install: ## Install dependencies
	@composer install

test: ## Runs tests
	@vendor/bin/phpunit --colors

cs: ## Fixes coding style
	@vendor/bin/php-cs-fixer fix

static-analysis: ## Runs a static analysis
	@vendor/bin/psalm

help:
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: help install test cs static-analysis
