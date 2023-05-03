Description of import of Horde libraries
# Clone the Horde Git Tools repository and install. You will need
  this for future updates:
    https://github.com/horde/git-tools
# Make sure to follow the #Configuration step mentioned in the URL above. In
  particular make sure to set the 'git_base' config option in conf.php
# Go into the repository cloned above and perform the following:
    bin/horde-git-tools git clone
  (Go for a coffee, this will take a while)
# Checkout the latest stable version for all repos, currently 5.2:
    bin/horde-git-tools git checkout FRAMEWORK_5_2
# Copy the following script and store it on /tmp, change it's execute bit(chmod 777), and run it,
  passing in your path to Horde (the directory you've cloned the repository):
    /tmp/copyhorde.sh ~/git/base/directory/from/step/2

Notes:
* 2023-01-20 Applied patch https://github.com/horde/Util/pull/10
* 2023-01-20 Horde/Mail is copied from https://github.com/bytestream/Mail/tree/v2.7.1 for PHP 8.1 compatibility

====
#!/bin/sh

source=$1
target=./lib/horde

echo "Copy Horde modules from $source to $target"

modules="Crypt_Blowfish Exception Idna Imap_Client Mail Mime Secret Socket_Client Stream Stream_Filter Stream_Wrapper Support Text_Flowed Translation Util"

rm -rf $target/locale $target/framework
mkdir -p $target/locale $target/framework/Horde

for module in $modules
do
  echo "Copying $module"
  cp -Rf $source/$module/lib/Horde/* $target/framework/Horde
  locale=$source/$module/locale
  if [ -d $locale ]
  then
    cp -Rf $locale/* $target/locale
  fi
done

Local modifications:
- lib/Horde/Imap/Client/Exception/ServerResponse.php has been minimally modified for php80 compatibility
  The fix applied is already upstream, see https://github.com/horde/Imap_Client/pull/13 and it's available
  in Imap_Client 2.30.4 and up. See MDL-73405 for more details.

Notes:
* 2023-01-30 Applied patch https://github.com/horde/Util/pull/11. See MDL-76412 for more details.
