About this theme
================

This is the Bootstrap theme for Iomad

* package   Moodle Iomad Bootstrap theme
* copyright 2014 Bas Brands. www.sonsbeekmedia.nl
* author   Bas Brands
* license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


JavaScript Libraries

html5shiv.js
------------
To provide backwards compatibility for HTML5 for Internet Explorer 7 (IE7) and Internet
Explorer 8 (IE8) a javascript library call /javascript/html5shiv.js was added. This
JavaScript converts HTML tags and CSS into Tag that are understood by IE7 and IE8.
The config.php makes sure these libraries are only loaded for IE7 and IE8.

bootstrap.js
--------------------------------------------------------------
This is the Bootstrap JavaScript file created by @fat and @mdo


Updating Bootstrap and Libraries
========================================

bootstrap
-----------------
This theme uses the original unmodified version 3.0.3 Bootstrap less files. These are
Object Oriented CSS files. The bootstrap repository is available on:

https://github.com/twbs/bootstrap.git

To update to the latest release of Bootstrap remove all files from less/bootstrap,
download the new less files and store them in less/bootstrap
Inclusion of bootstrap files is configured in less/moodle.less. To generate the new
Moodle CSS read /less/README

html5shiv.js
------------
This theme uses the original unmodified html5shiv.js JavaScript library to enable HTML5 tags in IE7 and IE8.
This library is available on:

https://github.com/aFarkas/html5shiv/blob/master/src/html5shiv.js

To update to the latest release of html5shiv download and replace:
javascript/html5shiv.js


Licenses & Authors
==================

Bootstrap Copyright and license
---------------------------------------
Authors: Mark Otto, Jacob Thornton
URL: http://getbootstrap.com/
License:

Copyright 2012 Twitter, Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this work except in compliance with the License.
You may obtain a copy of the License in the LICENSE file, or at:

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

Html5shiv.js
------------
Author: Sjoerd Visscher
URL: http://en.wikipedia.org/wiki/HTML5_Shiv, https://github.com/aFarkas/html5shiv
License: MIT/GPL2 Licensedc
