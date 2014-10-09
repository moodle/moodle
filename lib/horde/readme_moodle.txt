Description of import of Horde libraries

# Download the Horde git repository. You will probably want to keep this
  around for future updates:
    git clone git@github.com:horde/horde.git
# Checkout the version of horde you require:
    git checkout horde-5.2.1
# Copy the following script, change it's execute bit, and run it, passing
  in your path to Horde:
    /tmp/copyhorde.sh ~/git/ext/horde/framework

====
#!/bin/sh

source=$1/framework
target=./lib/horde

echo "Copy Horde modules from $source to $target"

modules="Crypt_Blowfish Exception Imap_Client Mail Mime Secret Socket_Client Stream Stream_Filter Stream_Wrapper Support Text_Flowed Translation Util"

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
