#!/bin/bash

# Package
cat ./dist/snap-custom-elements/runtime.js \
./dist/snap-custom-elements/polyfills.js \
./dist/snap-custom-elements/scripts.js \
./dist/snap-custom-elements/main.js > snap-ce.js

echo "Packaged project into snap-ce.js"
