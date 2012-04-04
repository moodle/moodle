#!/bin/bash

CLIDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
UTIL="$CLIDIR/util.php"

echo "Building phpunit.xml and initialising test database..."

php $UTIL --buildconfig
RESULT=$?
if [ $RESULT -gt 0 ] ; then
    exit $RESULT
fi

php $UTIL --drop
RESULT=$?
if [ $RESULT -gt 0 ] ; then
    exit $RESULT
fi

php $UTIL --install
RESULT=$?
if [ $RESULT -gt 0 ] ; then
    exit $RESULT
fi
