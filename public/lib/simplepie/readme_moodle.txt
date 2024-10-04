Description of SimplePie library import into Moodle

Obtained from https://github.com/simplepie/simplepie/releases/

To upgrade this library:
1. Download the latest release of SimplePie from https://github.com/simplepie/simplepie/releases/
2. Remove everything inside lib/simplepie/ directory except readme_moodle.txt (this file) and moodle_simplepie.php.
3. Extract the contents of the release archive into a directory.
4. Move the following files/directories from the extracted directory into lib/simplepie:
    - CHANGELOG.md
    - composer.json
    - LICENSE.txt
    - README.markdown
    - src/
5. That should leave you with just the following. Do not move them. If there is any difference,
   check if they also need to be moved and update this doc:
    - autoloader.php
    - db.sql
    - idn (This is a third-party library that SimplePie can optionally use. We don't use this in Moodle)
    - library

Changes:
  * None. This import contains _NO_CHANGES_ to the simplepie.inc file, changes are
    controlled through OO extension of the classes instead.
