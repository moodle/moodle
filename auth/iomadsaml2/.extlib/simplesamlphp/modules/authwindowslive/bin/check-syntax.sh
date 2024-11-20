#!/usr/bin/env bash

PHP='/usr/bin/env php'
RETURN=0

# check PHP files
for FILE in `find lib tests www -name "*.php"`; do
    $PHP -l $FILE > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo "Syntax check failed for ${FILE}"
        RETURN=`expr ${RETURN} + 1`
    fi
done

exit $RETURN
