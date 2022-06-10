#!/bin/bash

/bin/cp -fa /src/public /sockets/;

sed 's|DISPLAY_ERRORS|'$DISPLAY_ERRORS'|g;s|SOCKET|'$SOCKET'|g' /usr/local/etc/php-fpm.conf > /etc/php-fpm.conf;

/usr/local/sbin/php-fpm --nodaemonize -c /src/contrib/php.ini -y /etc/php-fpm.conf
