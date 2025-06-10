#!/bin/bash

# Check if UglifyJS is installed globally
if ! npm list -g uglify-js > /dev/null; then
  echo "UglifyJS is not installed globally."
  echo "Please run 'npm install -g uglify-js' to install it."
  exit 1
fi

# Clear the /build directory
rm -rf ./build/*
echo "Cleared /build directory."

# Ensure build directory exists
mkdir -p ./build

# Minify each JavaScript file in the /src directory
for file in ./src/*.js; do
  filename=$(basename -- "$file")
  basename="${filename%.*}"
  
  # Uglify the file and create a source map
  uglifyjs "$file" --compress --mangle --output "./build/${basename}.min.js" --source-map "url='${basename}.min.js.map',filename='./build/${basename}.min.js.map'"
  
  echo "Processed $file"
done

echo "All files have been minified and moved to /build directory."
