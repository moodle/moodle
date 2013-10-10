Description of import of various YUI libraries into Moodle:

1/ YUI2 version 2.9.0:
* copy of 2in3 without older version
* exact version specified in lib/setup.php

2/ YUI3 version 3.13.0:
* full copy of the "build" directory. Unit test code coverage files (*-coverage.js)
  are removed but no other changes are made.
* make sure there are no @VERSION@ leftovers - replace them with current version
* exact version specified in lib/setup.php
* update lib/thrirdpartylibs.xml
* verify our simpleyui rollup contents in /theme/yui_combo.php

Code downloaded from:
http://yuilibrary.com
