Description of XMLRPC for PHP library import into Moodle.

Source: https://github.com/gggeek/phpxmlrpc

This library provides XMLRPC client and server support
from PHP code. It's a modern replacement for the old
(removed from core since PHP 8.0) xmlrpc extension.

To update:
- Pick a release of the library @ https://github.com/gggeek/phpxmlrpc/releases.
- Download or checkout it.
- Delete the contents on the lib/phpxmlrpc directory (but this file) completely.
- Copy the /src directory contents of the library to lib/phpxmlrpc.
- Remove the following files:
  - Autoloader.php
- Edit this file and update the release and commit details below.
- Edit lib/thirdpartylibs.xml and update the information details too.

Current version imported: 4.8.1 (c74cc31)

Local changes:
 * readme_moodle.txt - this file ;-)
