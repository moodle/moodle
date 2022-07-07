Description of importing the codemirror library into Moodle.

NOTE 2: To make it more readable, in this explanation [LIBRARYPATH] means:
    [PATH TO YOUR MOODLE]/lib/editor/atto/plugins/html/yui/src/codemirror

1 Download the latest codemirror code somewhere (example /tmp/cm) using: npm install codemirror
  (note down the version number displayed by the command, you'll need it later)

2 Then copy the following files to your local Moodle directory:

cp node_modules/codemirror/lib/codemirror.js [LIBRARYPATH]/js
cp node_modules/codemirror/mode/css/css.js [LIBRARYPATH]/js
cp node_modules/codemirror/mode/htmlmixed/htmlmixed.js [LIBRARYPATH]/js
cp node_modules/codemirror/mode/javascript/javascript.js [LIBRARYPATH]/js
cp node_modules/codemirror/mode/xml/xml.js [LIBRARYPATH]/js

3 Update the verison number in:
    [PATH TO YOUR MOODLE]/var/www/html/m/master2/lib/editor/atto/plugins/html/thirdpartylibs.xml

3 rebuild the module by:
    cd [PATH TO YOUR MOODLE]/lib/editor/atto/plugins/html/yui/src/
    grunt shifter


