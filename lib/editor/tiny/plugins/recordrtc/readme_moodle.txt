lamejs 1.2.1
--------------
https://github.com/zhuker/lamejs

Instructions to import lamejs into Moodle:

1. Download the latest lamejs code somewhere (example /tmp/lamejs) using:

mkdir -p /tmp/lamejs
cd /tmp/lamejs
npm install lamejs --save-dev

Note down the version number displayed by the command, to update lib/editor/tiny/plugins/recordrtc/thirdpartylibs.xml accordingly.

If the command does not output a version number, the version number can be found in package.json. You can simply open package.json
using your desired editor and look for the version number of lamejs. Alternatively, you may use the following commands:
- MacOS:
    cat package.json | grep lamejs
- Linux:
    cat package.json | grep lamejs
    or
    jq -r '.devDependencies."lamejs"' package.json

2. Copy the following file to your local Moodle directory, to replace the old one:

cp /tmp/lamejs/node_modules/lamejs/lame.all.js [PATH TO YOUR MOODLE]/lib/editor/tiny/plugins/recordrtc/amd/src/lame.all.js

3. Add the following code to the bottom of the `lib/editor/tiny/plugins/recordrtc/amd/src/lame.all.js` file:

export default lamejs;

4. Update the new version in the `lib/editor/tiny/plugins/recordrtc/thirdpartylibs.xml` file.
