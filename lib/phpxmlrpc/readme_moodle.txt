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

Current version imported: 4.10.1 (ac18e31)

Local changes:
  * 2023/01/26 - Server.php and Value.php files have minor changes for PHP 8.2 compatibility. See MDL-76415 for more details.
    Since version 4.9.1, the phpxmlrpc already has the fix, so if someone executing the upgrading version and
    it has the patch, please ignore this note.
  * 2023-01-31 lib/phpxmlrpc/* has minor changes for PHP 8.2 compatibility. See MDL-76412 for more details.
    Since version 4.9.5, phpxmlrpc already has the fix, so if someone executing the upgrading version and
    it has the patch, please ignore this note.
  * 2023-01-31 Applied patch https://github.com/gggeek/phpxmlrpc/pull/110. See MDL-76412 for more details.
  * readme_moodle.txt - this file ;-)
