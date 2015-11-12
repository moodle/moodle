Description of Twitter bootstrap import into Moodle

Twitter bootstrap
-----------------
The bootstrap theme uses the original unmodified version 2.3.0 Twitter bootstrap less files. These are
Object Oriented CSS files. The bootstrap repository is available on:

https://github.com/twitter/bootstrap.git

To update to the latest release of twitter bootstrap:
* remove all files from less/bootstrap,
* download the new less files and store them in less/bootstrap
* Apply change in MDL-42195 (We don't want responsive images by default).
* Apply change in MDL-48328 (We need to reset the width of the container directly, in ./less/bootstrap/navbar.less, using the calculated value found in ./less/bootstrap/mixin.less).
* regenerate files using recess: recess --compile --compress moodle.less > ../style/moodle.css **
* regenerate files using recess: recess --compile --compress editor.less > ../style/editor.css **
* update ./thirdpartylibs.xml

** If you want to make changes to the .css generated from these .less files then you
need to install recess (https://github.com/twitter/recess) to compile the .less files,
then run these commands in the bootstrapbase/less/ folder:

bootstrap.js
------------
Version: 2.3.0

An alteration was made to the JavaScript to allow nested navigation to work properly on small screens (MDL-51819).
Bootstap 3 does away with nested menus (https://github.com/twbs/bootstrap/pull/6342), So a completely different solution
may be required if we upgrade this further.

html5shiv.js
------------
This theme uses the original unmodified html5shiv.js JavaScript library to enable HTML5 tags in IE7 and IE8.
This library is available on:

https://github.com/aFarkas/html5shiv/blob/master/src/html5shiv.js

To update to the latest release of html5shiv:
* download and replace: javascript/html5shiv.js
* update ./thirdpartylibs.xml
