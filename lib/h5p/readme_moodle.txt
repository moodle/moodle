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

Downloaded version: 1.23.1 release

=== 3.8 ===
* In order to allow the dependency path to be overridden by child H5PCore classes, a couple of minor changes have been added to the
h5p.classes.php file:
    - Into the getDependenciesFiles method, the line 2435:
        $dependency['path'] = 'libraries/' . H5PCore::libraryToString($dependency, TRUE);

      has been changed to:
      	$dependency['path'] = $this->getDependencyPath($dependency);

     - The method getDependencyPath has been added (line 2455). It might be rewritten by child classes.
A PR has been sent to the H5P library with these changes:
https://github.com/h5p/h5p-php-library/compare/master...andrewnicols:libraryPathSubclass
Hopefully, when upgrading, these patch won't be needed because it will be included in the H5P library by default.