H5P PHP library
---------------

Downloaded last release from:  https://github.com/h5p/h5p-php-library/tags

Import procedure:
 * Remove the content in this folder (but the readme_moodle.txt)
 * Copy all the files from the folder repository in this directory.

Removed:
 * composer.json
 * .gitignore
 * .travis.yml

Changed:
 1. Replace the $_SESSION references with $SESSION. That implies that the information is saved to backends, so only the Moodle one
    should be used by core (core should be free from $_SESSION and always use $SESSION).
    More specifically, in h5p.classes.php file, into hashToken() method:
        * Declare the global $SESSION.
        * Change all the $_SESSION by $SESSION.
        * Change all the $_SESSION['xxxx'] by $SESSION->xxxx.
    A script for testing this part can be found in MDL-68068

2. Add namespace to this library to avoid collision. It means:
  - Add the "namespace Moodle;" line at the top of all the h5p*.php files in the root folder.
  - Replace \H5Pxxx uses to H5Pxxx (for instance, in h5p-default-storage.class.php there are several references to \H5PCore that
    must be replaced with H5PCore).
  - Add "use ZipArchive;" in h5p.classes.h5p (check that it's still used before replacing it when upgrading the library).

3. Check if there are changes in the getLocalization() method in h5p.classes.php and update lang/en/h5p.php accordingly.
   If there are changes, check the t() method in h5p/classes/framework.php too (updating or adding new ones).
