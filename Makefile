QA        = docker run --rm --workdir=/project -v `pwd`:/project jakzal/phpqa:php8.1
ARTEFACTS = var/artefacts

# This file allows one to run tests on docker like they do on gitlab.

##
## Tests
## -----
##

tests: tu

tu: ## Run unit tests
	$(QA) vendor/bin/phpunit

tu-report: ## Run unit tests
	# use xdebug instead of phpdbg to generate coverage because phpdbg crashes
	$(QA) sh -c 'pecl install xdebug && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20210902/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini && php -d xdebug.mode=coverage -d memory_limit=-1 ./vendor/bin/phpunit --coverage-text --coverage-html $(ARTEFACTS)/coverage/'

.PHONY: tu tu-report

##
## Quality assurance
## -----------------
##


lint: ## Lints twig and yaml files
lint: lint-twig lint-yaml

lint-twig: ## Lint twig templates
	$(QA) bin/console lint:twig templates

lint-yaml: ## Lint twig templates
	$(QA) bin/console lint:yaml config
	$(QA) bin/console lint:yaml src
	$(QA) bin/console lint:yaml fixtures

security: ## Check security of your dependencies (https://security.sensiolabs.org/)
	$(QA) local-php-security-checker

phploc: ## PHPLoc (https://github.com/sebastianbergmann/phploc)
	$(QA) phploc src/

pdepend: ## PHP_Depend (https://pdepend.org)
	$(QA) pdepend \
		--summary-xml=$(ARTEFACTS)/pdepend_summary.xml \
		--jdepend-chart=$(ARTEFACTS)/pdepend_jdepend.svg \
		--overview-pyramid=$(ARTEFACTS)/pdepend_pyramid.svg \
		src/

phpmd: ## PHP Mess Detector (https://phpmd.org)
	$(QA) phpmd src/ html .phpmd.xml --reportfile $(ARTEFACTS)/phpmd/index.html --ignore-violations-on-exit

php_codesnifer: ## PHP_CodeSnifer (https://github.com/squizlabs/PHP_CodeSniffer)
	$(QA) phpcs -v --standard=.phpcs.xml src

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
	$(QA) phpcpd src

phpdcd: ## PHP Dead Code Detector (https://github.com/sebastianbergmann/phpdcd)
	$(QA) phpdcd src

phpmetrics: ## PhpMetrics (http://www.phpmetrics.org)
	$(QA) phpmetrics --report-html=$(ARTEFACTS)/phpmetrics --exclude=migrations .

php-cs-fixer: ## php-cs-fixer (http://cs.sensiolabs.org)
	$(QA) php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --using-cache=no --verbose --diff --stop-on-violation

apply-php-cs-fixer: ## apply php-cs-fixer fixes
	$(QA) php-cs-fixer fix --config=.php-cs-fixer.dist.php --using-cache=no --verbose --diff

phpstan: ## PHP Static Analysis Tool (https://github.com/phpstan/phpstan)
	$(QA) phpstan analyse -c phpstan.neon -l2 src

twigcs: ## twigcs (https://github.com/allocine/twigcs)
	$(QA) twigcs lint templates


.PHONY: lint lint-yaml lint-twig phploc pdepend phpmd php_codesnifer phpcpd phpdcd phpmetrics php-cs-fixer apply-php-cs-fixer phpstan



.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
