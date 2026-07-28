Description of Twitter bootstrap import into Moodle

Twitter bootstrap
-----------------

Sass:
This theme uses Bootstrap frontend toolkit.
The Bootstrap repository is available on:

https://github.com/twbs/bootstrap

To update to the latest release of twitter bootstrap:

* download bootstrap to your home folder
* remove folder theme/boost/scss/bootstrap
* copy the scss files from ~/bootstrap/scss to theme/boost/scss/bootstrap
* comment out left: 0; from .popover {} in scss/bootstrap/_popover.scss. In RTL mode this prevents popovers from showing and it is not required in LTR mode.
* comment out this line in theme/boost/scss/_print.scss
    @page {
       size: $print-page-size;
    }
  It breaks when compiled with phpscss.
* update ./thirdpartylibs.xml
* follow the instructions in admin/tool/component_library/readme_moodle.txt to update the Bootstrap documentation there.

Javascript:

* remove folder theme/boost/amd/src/bootstrap
* copy the js files from ~/bootstrap/js/src to theme/boost/amd/src/bootstrap (including the subfolder)
* copy index.js from ~/bootstrap/js to theme/boost/amd/src
* edit theme/boost/amd/src/index.js and update import path (src -> bootstrap)
* Moodle core includes the popper.js library, make sure each of the new Bootstrap js files
includes the 'core/popper' library instead of 'popper.js'. For current version these files were: tooltip.js and dropdown.js
* update ./thirdpartylibs.xml to include all new Bootstrap js files
* run "grunt ignorefiles" to prevent linting errors appearing from the new Bootstrap js files.
* in folder theme/boost run "grunt amd" to compile the bootstrap JS
* in folder theme/boost run "grunt css" to compile scss
