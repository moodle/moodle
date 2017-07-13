Description of import of various YUI libraries into Moodle:

1/ YUI2 version 2.9.0:
* copy of 2in3 without older version
* exact version specified in lib/setup.php

2/ YUI3 version 3.17.2:
* full copy of the "build" directory. Unit test code coverage files (*-coverage.js)
  are removed but no other changes are made.
* make sure there are no @VERSION@ leftovers - replace them with current version
* exact version specified in lib/setup.php
* update lib/thirdpartylibs.xml
* verify our simpleyui rollup contents in /theme/yui_combo.php

If you need to patch the YUI library between its official releases, you *must* read
http://docs.moodle.org/dev/YUI/Patching.

3/ YUI3 Gallery version gallery-2013.10.02-20-26:
* selective copy of the "build" directory for the checked out tag of yui3-gallery.
  Unit test code coverage files (*-coverage.js) are removed but no other changes are made.
* update lib/thirdpartylibs.xml
* Note: versions in the gallery modules may differ from the tagged version but will be the
  latest at the time the module was tagged.
Currently supported gallery modules:
* gallery-sm-treeview*

Code downloaded from:
http://yuilibrary.com
