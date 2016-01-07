#!/bin/bash
BASEDIR=$(dirname $0)
/usr/bin/php ${BASEDIR}/cliZend.php -a default/console/$1 -e production