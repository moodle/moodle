#!/bin/bash

EXPECTED_ARGS=4

if [ $# -ne $EXPECTED_ARGS ]; then
	echo "Missing arguments!"
	printf "Usage: \n \t$0 {existing build number} {new biuld number} {existing release number} {new release number}"
	printf "\nExample: \n \t$0 2014102807 2015012507 4.0.02 4.0.03\n\n"
	exit 1;
fi


EXISTING_BUILD_NUMBER=$1
NEW_BUILD_NUMBER=$2
EXISTING_RELEASE_NUMBER=$3
NEW_RELEASE_NUMBER=$4

FILES=`grep $EXISTING_BUILD_NUMBER ./* -R -l`

for filename in $FILES; do
	echo $filename
	sed -i "" -e "s/$EXISTING_BUILD_NUMBER/$NEW_BUILD_NUMBER/g" $filename
	sed -i "" -e "s/$EXISTING_RELEASE_NUMBER/$NEW_RELEASE_NUMBER/g" $filename
done

git status
