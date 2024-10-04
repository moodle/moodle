Description of updating JS-Beautify and CodeMirror libraries into Moodle.

There is a build script that should automate the process of updating the libraries.
It is located in the build directory and is called build.sh.
It requires the following pre-requisites: jq, npm and npx.
Most Moodle LMS development systems should have at least npm and npx installed.
However, jq is not a common tool and may need to be installed separately.

For example, on Ubuntu you would use:
sudo apt-get install jq

And on macOS with Homebrew:
brew install jq

Once you have the pre-requisites installed, you can run the build script.
from the root of the plugin (e.g lib/editor/tiny/plugins/html), run:
./build/build.sh

Once the script has run, you should see the following files updated:
* lib/editor/tiny/plugins/html/amd/src/beautify/beautify.js
* lib/editor/tiny/plugins/html/amd/src/beautify/beautify-css.js
* lib/editor/tiny/plugins/html/amd/src/beautify/beautify-html.js
* lib/editor/tiny/plugins/html/amd/src/codemirror/codemirror-lazy.js

Next, you need to run the build script for the Moodle plugin.
From the root of the plugin (e.g lib/editor/tiny/plugins/html), run:
cd amd
grunt

The build.sh script should display the latest versions of the libraries in order to
update thirdpartylibs.xml with the new version numbers.

