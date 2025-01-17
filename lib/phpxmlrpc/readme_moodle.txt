Description of XMLRPC for PHP library import into Moodle.

Source: https://github.com/gggeek/phpxmlrpc

This library provides XMLRPC client and server support
from PHP code. It's a modern replacement for the old
(removed from core since PHP 8.0) xmlrpc extension.

To update:
- Pick a release of the library @ https://github.com/gggeek/phpxmlrpc/releases.
- Download or checkout it.
- Delete the contents of the lib/phpxmlrpc/src directory completely.
- Copy the /src directory of the library to lib/phpxmlrpc/.
- Remove the following files:
  - lib/phpxmlrpc/src/Autoloader.php
- Copy the following files to lib/phpxmlrpc:
  - composer.json
  - license.txt
  - README.md
  - NEWS.md
- Edit lib/thirdpartylibs.xml and update the information details too.
