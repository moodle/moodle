Description of import of various YUI libraries into Moodle:

1/ YUI2 version 2.9.0:
* copy of 2in3 without older version
* exact version specified in lib/setup.php
* Accessibility change. Which folder or file has no children, remove unnecessary tab stop.
  lib/yuilib/2in3/2.9.0/build/yui2-treeview/yui2-treeview.js

2/ YUI3 version 3.18.1:
* Full copy of the "build" directory. Unit test code coverage files (*-coverage.js)
  are removed but no other changes are made.
  Useful command: find . -type f -name "*-coverage.js" -delete
* Make sure there are no @VERSION@ leftovers - replace them with current version
  Useful command: find . -type f -exec sed -i "s/@VERSION@/3.18.1/g" {} \;
* Exact version specified in lib/setup.php
* Update yuilib version in lib/thirdpartylibs.xml with 3.18.1
* Verify our simpleyui rollup contents in /theme/yui_combo.php.
  e.g. http://[yourmoodle]/theme/yui_combo.php?rollup/3.18.1/yui-moodlesimple.js

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
