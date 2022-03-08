H5P PHP library
---------------

Downloaded last release from:  https://github.com/h5p/h5p-php-library/tags

Import procedure:

- Copy all the files from the folder repository in this directory.

Removed:
 * composer.json
 * .gitignore

Added:
 * readme_moodle.txt

NOTICE:
 * We are following the composer version, 1.24.x according the suggestion from Joubel.

Changes:
1. In order to allow the dependency path to be overridden by child H5PCore classes, a couple of minor changes have been added to the
h5p.classes.php file:
    - Into the getDependenciesFiles method, the line 2435:
        $dependency['path'] = 'libraries/' . H5PCore::libraryToString($dependency, TRUE);

      has been changed to:
        $dependency['path'] = $this->getDependencyPath($dependency);

     - The method getDependencyPath has been added (line 2460). It might be rewritten by child classes.
A PR has been sent to the H5P library with these changes:
https://github.com/h5p/h5p-php-library/compare/master...andrewnicols:libraryPathSubclass
Hopefully, when upgrading, these patch won't be needed because it will be included in the H5P library by default.


2. Replace the $_SESSION references to $SESSION. That implies that the information is saved to backends, so only the Moodle one
should be used by core (core should be free from $_SESSION and always use $SESSION).

h5p.classes.php file:
  - Into hashToken method:
    Declare the global $SESSION.
    Change all the $_SESSION by $SESSION.
    Change all the $_SESSION['xxxx'] by $SESSION->xxxx.
A script for testing this part can be found in MDL-68068


3. Upgrade and patch JQuery library.
Once https://github.com/h5p/h5p-php-library/issues/83 gets integrated in
H5P PHP Library (upgrading the JQuery version), this change won't be needed.

3.1. Prepare the patched JQuery 1.12.4 library following these steps:
  a) Download the uncompressed JQuery Core 1.12.4 from https://code.jquery.com/jquery-1.12.4.js
  b) Add the patch in https://snyk.io/vuln/SNYK-JS-JQUERY-174006 to the downloaded file.
  You'll need to replace this code (line 212):

        // Prevent never-ending loop
        if ( target === copy ) {
          continue;
        }

  to:
        // Prevent Object.prototype pollution
        // Prevent never-ending loop
        if ( name === "__proto__" || target === copy ) {
          continue;
        }
  c) Minify the patched jquery-1-12.4.js.

3.2. Edit h5p/h5plib/v124/joubel/core/js/jquery.js and replace the JQuery piece of code
(at the beginning of the file, above the comment "// Snap this specific version of jQuery into H5P. jQuery.noConflict will")
with the previous patched and minified JQuery version.

3.3. Remove the following comment in h5p/h5plib/v124/joubel/core/js/jquery.js:

/**
 * jQuery v1.9.1
 *
 * @member
 */


4. Add namespace to this library to avoid collision. It means:

- Add the "namespace Moodle;" line at the top of all the h5p*.php files in the root folder.
- Replace \H5Pxxx uses to H5Pxxx (for instance, in h5p-default-storage.class.php there are several references to \H5PCore that
must be replaced with H5PCore).
- Add "use ZipArchive;" in h5p.classes.h5p (check that it's still used before replacing it when upgrading the library).
