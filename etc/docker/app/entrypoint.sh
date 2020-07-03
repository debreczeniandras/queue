#!/bin/bash

set -e

case "$1" in
    "install")
        exec composer install ${@:2} ;;
    "root")
        exec /bin/bash ;;
    "console")
        php -d memory_limit=-1 bin/console ${@:2} ;;
    "shell")
        "/bin/bash" ;;
    "composer")
        php -d memory_limit=-1 /usr/local/bin/composer ${@:2} ;;
    ""|"php-fpm")
      tail --pid $$ -n0 -v -F var/logs/*.log & exec php-fpm ;;
    *)

    "$@" ;;
esac
