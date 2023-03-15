# Silence output slightly
# .SILENT:

PHP ?= $(shell which php)
PHPCSF := ./vendor/bin/php-cs-fixer
PHPSTAN := ./vendor/bin/phpstan
TWIGCS := ./vendor/bin/twigcs

BUNDLES := BlogBundle DublinCoreBundle EditorBundle FeedbackBundle MakerBundle MediaBundle SolrBundle UserBundle UtilBundle

## -- Help
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9._-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | sed -e 's/^.*Makefile[^:]*://' | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## -- Coding standards fixing

fix: ## Fix the code with the CS rules
	$(PHPCSF) fix $(path)

fix.cc: ## Remove the PHP CS Cache file
	rm -f .php_cs.cache

fix.all: fix.cc fix ## Ignore the CS cache and fix the code with the CS rules

fix.list: ## Check the code against the CS rules
	$(PHPCSF) fix --dry-run -v $(path)

## -- Coding standards checking

lint-all: stan.cc stan twigcs

twigcs: ## Check the twig templates against the coding standards
	$(TWIGCS) $(BUNDLES)

stan: ## Run static analysis
	$(PHPSTAN) --memory-limit=1G analyze $(BUNDLES)

stan.cc: ## Clear the static analysis cache
	$(PHPSTAN) clear-result-cache

stan.baseline: ## Generate a new phpstan baseline file
	$(PHPSTAN) --memory-limit=1G analyze --generate-baseline $(BUNDLES)

