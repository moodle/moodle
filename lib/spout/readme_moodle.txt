#!/bin/sh
#
# Description of import of Horde libraries
#

wget https://codeload.github.com/box/spout/zip/v2.4.3
unzip v2.4.3
rm v2.4.3
rm spout-2.4.3/composer.json
rm -rf src
mv -f spout-2.4.3/* .
rm -r spout-2.4.3/

