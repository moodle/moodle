Description of password_compat import into Moodle:
==================================================

Imported from: https://github.com/ircmaxell/password_compat/releases/tag/v1.0.4
Copyright: (c) 2012 Anthony Ferrara
License: MIT License

Files used from the library:
* lib/password.php  > lib/password.php
* test/Unit/*       > tests/

Added:
* None.

Our changes:
* Added the following require_once() to the test files:
    global $CFG;
    require_once($CFG->dirroot . '/lib/password_compat/lib/password.php');

Library description:
====================

Compatibility with the password_* functions being worked on for PHP 5.5.

This library requires PHP >= 5.3.7 due to a PHP security issue prior to that
version.

See the RFC (https://wiki.php.net/rfc/password_hash) for more information.

Latest code available from https://github.com/ircmaxell/password_compat/
under MIT license.
