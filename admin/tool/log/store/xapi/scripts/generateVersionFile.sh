#!/usr/bin/env sh
release="$TRAVIS_TAG"

year=$(date +"%Y")

today=$(date +"%Y-%m-%d")
todaysReleases=$(git log --tags --simplify-by-decoration --pretty="format:%ai %d" | sort -r | grep "$today" | wc -l | tr -d '[:space:]')

versionPrefix=$(date +"%Y%m%d")
versionSuffix=$(printf %02d $todaysReleases)
version="$versionPrefix$versionSuffix"

template=$(cat ./scripts/templateVersionFile)
eval "echo \"$template\"" > version.php
