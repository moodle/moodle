Description of Html2Text v4.3.1 library import into Moodle

Please note that we override some mb_* functions in Html2Text's namespace at
run time. Until Html2Text adds some sort of fallback for the mb_* functions
(or we make mbstring a hard requirement) we are forced to do this so that people
running PHP without mbstring don't see nasty undefined function errors.

Instructions
------------
1. Download the latest release of Html2Text from https://github.com/mtibben/html2text/releases/
2. Extract the contents of the release archive into a directory.
3. Copy src/Html2Text.php to lib/html2text/

Imported from: https://github.com/mtibben/html2text/releases/
