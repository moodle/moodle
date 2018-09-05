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
* regenerate css files using grunt
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

variables.less
------------
The calculations for the following variables have been enclosed in parentheses in order for them to be correctly output
in the compiled CSS (MDL-53152):
* @fontSizeLarge
* @fontSizeSmall
* @fontSizeMini
* @inputHeight
* @navbarCollapseDesktopWidth
* @popoverArrowOuterWidth
* @gridRowWidth
* @gridRowWidth1200
* @gridRowWidth768

popovers.less
-------------
MDL-60250 - The '/*rtl:ignore*/' directive has been added to the 'left' attribute of the 'popover' class so that
the rtlcss-php tool (https://github.com/moodlehq/rtlcss-php) does not flip this to a 'right' attribute and cause
the popover to be misplaced on the page when the JavaScript calculates the postion of the popover and adds an
overriding inline CSS 'left' attribute which is fine in LTR languages but confuses it in RTL languages.
