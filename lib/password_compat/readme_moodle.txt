Description of password_compat import into Moodle:
==================================================

Imported from: https://github.com/ircmaxell/password_compat/commit/2a7b6355d27c65f7e0de1fbbc0016b5b6cd8226b
Copyright: (c) 2012 Anthony Ferrara
License: MIT License

Removed:
* README.md, LICENSE.md and composer.json files.
* bootstrap.php and phpunit.xml.dist files from test directory.

Added:
* None.

Our changes:
* Moved tests from test/Unit/ to tests/ directory.
* Removed tabs and trailing whitespace from test files.
* Added markTestSkipped() check to tests so they only run if password_compat is supported

Moodle commit history:
======================

MDL-35332   Initial commit


Library description:
====================

Compatibility with the password_* functions being worked on for PHP 5.5.

This library requires PHP >= 5.3.7 due to a PHP security issue prior to that
version.

See the RFC (https://wiki.php.net/rfc/password_hash) for more information.

Latest code available from https://github.com/ircmaxell/password_compat/
under MIT license.
