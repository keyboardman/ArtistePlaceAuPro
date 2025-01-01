.PHONY: install deploy

deploy:
		ssh vojo1147@heron.o2switch.net -f 'cd /home/vojo1147/repositories/artiste.placeaupro.fr && git pull origin master && make install'

install: vendor/autoload.php
		php bin/console d:m:m
		php bin/console importmap:install
		php bin/console asset-map:compile
		php bin/console c:c

vendor/autoload.php: composer.lock composer.json
		composer install --no-dev --optimize-autoloader
		touch vendor/autoload.php