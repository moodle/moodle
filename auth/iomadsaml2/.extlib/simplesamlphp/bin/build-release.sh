#!/usr/bin/env bash

set -e

VERSION=$1
REPOPATH=$2

if ! shift; then
    echo "$0: Missing required version parameter." >&2
    exit 1
fi

if [ -z "$VERSION" ]; then
    echo "$0: Empty version parameter." >&2
    exit 1
fi

if [ -z "$REPOPATH" ]; then
    REPOPATH="https://github.com/simplesamlphp/simplesamlphp.git"
fi

TAG="v$VERSION"
TARGET="simplesamlphp-$VERSION"

cd /tmp

if [ -a "$TARGET" ]; then
    echo "$0: Destination already exists: $TARGET" >&2
    exit 1
fi

umask 0022

git clone $REPOPATH $TARGET
cd $TARGET
git checkout $TAG
cd ..

if [ ! -x "$TARGET/composer.phar" ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=$TARGET
fi

# Downgrade composer to v1 to remain compatible with composer <1.8.5 (Debian 10)
php "$TARGET/composer.phar" self-update --1

# Set the version in composer.json
php "$TARGET/composer.phar" config version "v$VERSION" -d "$TARGET"

# Install dependencies (without vcs history or dev tools)
php "$TARGET/composer.phar" install --no-dev --prefer-dist -o -d "$TARGET"

cd $TARGET
npm install
npm audit fix
npm run build
cd ..

mkdir -p "$TARGET/config" "$TARGET/metadata" "$TARGET/cert" "$TARGET/log" "$TARGET/data"
cp -rv "$TARGET/config-templates/"* "$TARGET/config/"
cp -rv "$TARGET/metadata-templates/"* "$TARGET/metadata/"
rm -rf "$TARGET/.git"
rm -rf "$TARGET/node_modules"
rm "$TARGET/www/assets/js/stylesheet.js"*
rm "$TARGET/.editorconfig"
rm "$TARGET/.gitattributes"
rm -r "$TARGET/.github"
rm "$TARGET"/{,modules/*}/.php_cs.dist
rm "$TARGET"/{,modules/*}/codecov.yml
rm "$TARGET"/{,modules/*}/phpcs.xml
rm "$TARGET"/{,modules/*}/psalm.xml
rm "$TARGET"/{,modules/*}/.gitignore
rm "$TARGET"/{cache,config,metadata,locales}/.gitkeep
rm "$TARGET/composer.phar"
tar --owner 0 --group 0 -cvzf "$TARGET.tar.gz" "$TARGET"
rm -rf "$TARGET"

echo "Created: /tmp/$TARGET.tar.gz"
