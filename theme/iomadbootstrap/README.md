About this theme
================

![image1](pix/screenshot.jpg "Moodle Bootstrap Screenshot")

[![Build Status](https://travis-ci.org/bmbrands/theme_bootstrap.svg?branch=master)](https://travis-ci.org/bmbrands/theme_bootstrap)

Changelog:

version 2015110600: Fixed issue on group selection boxes in courses (thanks Emma Ridchardson).

This is the bootstrap theme for Moodle.

* package   Moodle Bootstrap theme
* copyright 2015 Bas Brands. www.sonsbeekmedia.nl
* authors   Bas Brands, David Scotson
* license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

This theme has been created with the help of:
Stuart Lamour, Mark Aberdour, Paul Hibbitts, Mary Evans, Joby Harding, Gareth J Barnard, Howard Miller

This theme is based on the Bootstrap CSS framework version 3.2.0
It contains all unmodified less* CSS sources from the Bootstrap CSS
framework in folder /less/bootstrap.
On top of the Bootstrap less CSS sources Moodle CSS is added to create this
theme.

Modify this theme
-----------------
Do not modify or clone this theme. If you want to customize this theme create a 
child theme, a simple child theme is available on:
https://github.com/bmbrands/theme_cerulean

Moodle versions
---------------
This theme works on Moodle 2.9 and newer


Supported browsers
------------------
IE9+
Recent versions of all modern browsers

Less CSS
--------
Less CSS is a Object Oriented way of writing CSS code. All Less CSS files
for this theme are stored in the /less folder. A developer can use recess
to generate the CSS files in the /style folder. For more
information read /less/README

JavaScript Libraries
====================

This theme only uses YUI libraries. If you would like to used any of the advance
Bootstrap3 JavaScript you can create a child theme that requires jQuery


Updating Bootstrap and Libraries
================================

bootstrap
-----------------
This theme uses the original unmodified version 3.1.1 Bootstrap less files. These are
Object Oriented CSS files. The bootstrap repository is available on:

https://github.com/twbs/bootstrap.git

To update to the latest release of Bootstrap remove all files from less/bootstrap,
download the new less files and store them in less/bootstrap
Inclusion of bootstrap files is configured in less/moodle.less. To generate the new
Moodle CSS read /less/README


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
