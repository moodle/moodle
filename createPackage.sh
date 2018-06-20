#!/bin/bash

BRANCH=`git branch | grep '*' |awk -F _ '{print $2}'`
VER=`cat local/kaltura/version.php |grep '>version' | awk '{print $3}' | awk -F ";" '{print $1}'`

echo "Generaating package for Kaltura_Video_Package_moodle"$BRANCH"_"$VER".zip\n"

FILENAME="Kaltura_Video_Package_moodle"$BRANCH"_"$VER".zip"

zip -r $FILENAME lib filter mod local blocks
