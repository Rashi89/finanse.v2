#!/bin/bash
set -eu
IFS=$'\n\t'

CURRDIR=$(dirname "$0")
CURRDIR=$(realpath "$CURRDIR")
cd "$CURRDIR"

set +e
OLDCPSR=$(find "$CURRDIR/composer.phar" -mtime +30 -print)
set -e
if [ ! -f "$CURRDIR/composer.phar" ] || [ -n "$OLDCPSR" ]
then
	curl --location --progress-bar --fail --show-error https://getcomposer.org/composer.phar --output "$CURRDIR/composer.phar"
fi

if [ ! -f "$CURRDIR/vendor/autoload.php" ]
then
	php "$CURRDIR/composer.phar" install --ansi --no-interaction --no-progress --no-suggest --prefer-dist
fi

set +e
OLDCSFX=$(find "$CURRDIR/php-cs-fixer.phar" -mtime +30 -print)
set -e
if [ ! -f "$CURRDIR/php-cs-fixer.phar" ] || [ -n "$OLDCSFX" ]
then
	VERSION=$(curl --location --progress-bar --fail --show-error https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/git/refs/tags | jq '.[-1].ref' | sed 's/"//g' | sed 's/refs\/tags\///g')
	RELEASE_URL="https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/${VERSION}/php-cs-fixer.phar"
	echo "DOWNLOAD : $RELEASE_URL"
	curl --location --progress-bar --fail --show-error "$RELEASE_URL" --output "$CURRDIR/php-cs-fixer.phar"
fi

printf "\n"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] RUN PHP CS FIXER"
php "$CURRDIR/php-cs-fixer.phar" fix -vvv --allow-risky=yes

set +e
OLDPSTN=$(find "$CURRDIR/phpstan.phar" -mtime +30 -print)
set -e
if [ ! -f "$CURRDIR/phpstan.phar" ] || [ -n "$OLDPSTN" ]
then
	RELEASE_URL=$(curl --location --progress-bar --fail --show-error https://api.github.com/repos/phpstan/phpstan/releases/latest | jq '.assets[0].browser_download_url' | sed 's/"//g')
	echo "DOWNLOAD : $RELEASE_URL"
	curl --location --progress-bar --fail --show-error "$RELEASE_URL" --output "$CURRDIR/phpstan.phar"
fi

printf "\n"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] RUN PHPSTAN"
php "$CURRDIR/phpstan.phar" analyse --configuration="$CURRDIR/phpstan.neon" --error-format=table

set +e
OLDPHMD=$(find "$CURRDIR/phpmd.phar" -mtime +30 -print)
set -e
if [ ! -f "$CURRDIR/phpmd.phar" ] || [ -n "$OLDPHMD" ]
then
	curl --location --progress-bar --fail --show-error https://phpmd.org/static/latest/phpmd.phar --output "$CURRDIR/phpmd.phar"
fi

printf "\n"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] RUN PHPMD"
php "$CURRDIR/phpmd.phar" "$CURRDIR/src" ansi "$CURRDIR/phpmd.xml"

set +e
OLDPSLM=$(find "$CURRDIR/psalm.phar" -mtime +30 -print)
set -e
if [ ! -f "$CURRDIR/psalm.phar" ] || [ -n "$OLDPSLM" ]
then
	RELEASE_URL=$(curl --location --progress-bar --fail --show-error https://api.github.com/repos/vimeo/psalm/releases/latest | jq '.assets[0].browser_download_url' | sed 's/"//g')
	echo "DOWNLOAD : $RELEASE_URL"
	curl --location --progress-bar --fail --show-error "$RELEASE_URL" --output "$CURRDIR/psalm.phar"
fi

printf "\n"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] RUN PSALM"
php "$CURRDIR/psalm.phar" --config="$CURRDIR/psalm.xml" --output-format=console --long-progress --stats --show-info=true


