#!/bin/bash
BASEDIR=$(dirname $0)
/usr/bin/php ${BASEDIR}/cli.php -a default/console/$1 -e production