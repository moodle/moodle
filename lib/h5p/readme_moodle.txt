H5P PHP library
---------------

Downloaded last release from: https://github.com/h5p/h5p-php-library/releases

Import procedure:

- Copy all the files from the folder repository in this directory.

Removed:
 * composer.json
 * .gitignore

Added:
 * readme_moodle.txt

Downloaded version: 1.24 release


=== 3.8 ===
1. In order to allow the dependency path to be overridden by child H5PCore classes, a couple of minor changes have been added to the
h5p.classes.php file:
    - Into the getDependenciesFiles method, the line 2435:
        $dependency['path'] = 'libraries/' . H5PCore::libraryToString($dependency, TRUE);

      has been changed to:
        $dependency['path'] = $this->getDependencyPath($dependency);

     - The method getDependencyPath has been added (line 2455). It might be rewritten by child classes.
A PR has been sent to the H5P library with these changes:
https://github.com/h5p/h5p-php-library/compare/master...andrewnicols:libraryPathSubclass
Hopefully, when upgrading, these patch won't be needed because it will be included in the H5P library by default.


2. As the mbstring extension is optional in Moodle, the following changes have been hardcoded to the library:
2.1. Comment the following methods in h5p.classes.php file where the extension_loaded('mbstring') is called:
    * isValidPackage
    * checkSetupErrorMessage
    * validateText
    * validateContentFiles

2.2. Change all the mb_uses straight to the core_text() alternatives. Version 1.24 has 3 ocurrences in h5p.classes.php
and 1 ocurrence in h5p-metadata.class.php.

3. Another PR has been sent to H5P library (https://github.com/h5p/h5p-php-library/pull/69) to fix some php74 minor problems. The same fix is being applied locally by MDL-67077. Once we import a new version, if it includes de fix, this won't be needed to reapply and can be removed.


The point 2 from above won't be needed once the mbstring extension becomes mandatory in Moodle. A request has been
sent to MDL-65809.
