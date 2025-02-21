#!/bin/bash

EXPECTED_ARGS=4

if [ $# -ne $EXPECTED_ARGS ]; then
	echo "Missing arguments!"
	printf "Usage: \n \t$0 {existing build number} {new build number} {existing release number} {new release number}\n\n"
	printf "IMPORTANT - the version number should follow these rules - YYYYMMDDII\n"
        printf "WHERE: YYYMMDD should be the release date of the major Moodle version for the current branch (e.g. for 3.10 it should be 20201109) and the II should be the build number for the plugin for that version\n"
        printf "For example, let's assume the next 3.12 release of moodle happens at 2025/12/01 and then we release a plugin compatible with that version, the version number should be set to 2025120100, and the second version of the plugin for 3.12 should have 2025120101\n"
        printf ""
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
