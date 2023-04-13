help:    ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

cs:      ## Run code style fixer
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix -vv
