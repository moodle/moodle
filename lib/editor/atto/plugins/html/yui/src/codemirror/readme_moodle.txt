Description of importing the codemirror library into Moodle.

NOTE: To make it more readable, in this explanation [LIBRARYPATH] means:
    [PATH TO YOUR MOODLE]/lib/editor/atto/plugins/html/yui/src/codemirror

1 Download the latest codemirror code somewhere (example /tmp/cm) using: npm install codemirror OR download the zip file
  (note down the version number displayed by the command, you'll need it later)

2 Then copy the following files to your local Moodle directory:
If using npm install:
[CODEMIRRORPATH] = node_modules

If using the zip file:
[CODEMIRRORPATH] = codemirror-X.XX.X

cp [CODEMIRRORPATH]/codemirror/lib/codemirror.js [LIBRARYPATH]/js
cp [CODEMIRRORPATH]/codemirror/mode/css/css.js [LIBRARYPATH]/js
cp [CODEMIRRORPATH]/codemirror/mode/htmlmixed/htmlmixed.js [LIBRARYPATH]/js
cp [CODEMIRRORPATH]/codemirror/mode/javascript/javascript.js [LIBRARYPATH]/js
cp [CODEMIRRORPATH]/codemirror/mode/xml/xml.js [LIBRARYPATH]/js

3 Rebuild the module by:
    cd [PATH TO YOUR MOODLE]/lib/editor/atto/plugins/html/yui/src/
    grunt shifter

4 Update the version number in (using the version noted down in #1):
    [PATH TO YOUR MOODLE]/lib/editor/atto/plugins/html/thirdpartylibs.xml
