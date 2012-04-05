#!/bin/bash

CLIDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
UTIL="$CLIDIR/util.php"

echo "Initialising test database and creating phpunit.xml configuration..."

DIGERROR=`php $UTIL --diag`
DIAG=$?
if [ $DIAG -eq 132 ] ; then
    php $UTIL --install
else
    if [ $DIAG -eq 133 ] ; then
        php $UTIL --drop
        RESULT=$?
        if [ $RESULT -gt 0 ] ; then
            exit $RESULT
        fi
        php $UTIL --install
    else
        if [ $DIAG -gt 0 ] ; then
            echo $DIGERROR
            exit $DIAG
        fi
    fi
fi

php $UTIL --buildconfig
