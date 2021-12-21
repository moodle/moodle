H5P Editor PHP Library
----------------------

Downloaded last release from: https://github.com/h5p/h5p-editor-php-library/releases

Import procedure:

- Copy all the files from the folder repository in this directory.
- In the method ns.LibrarySelector.prototype.appendTo (scripts/h5peditor-library-selector.js),
  comment the line "this.$selector.appendTo($element);" to avoid the display of the Hub Selector.
- Review strings in joubel/editor/language/en.js and compare them with
existing ones in lang/en/h5plib_vXXX.php: add the new ones and remove the
unexisting ones. Remember to use the AMOS script commands, such CPY, to copy
all the existing strings from the previous version. As you'll see, all the
strings in en.js have been converted following these rules:
  * Prefix  "editor:" has been added.
  * Keys have been lowercased.
- Add namespace to this library to avoid collision. It means:
  * Add the "namespace Moodle;" line at the top of all the h5peditor*.php files in the root folder.
  * Replace \H5Pxxx uses to H5Pxxx (for instance, in h5peditor-ajax.class.php there are several references to \H5PCore that
must be replaced with H5PCore).
  * Add "use stdClass;" in h5peditor.class.php and h5peditor-file.class.php (check that it's still used before replacing it when upgrading the library).



Removed:
 * composer.json
 * .gitignore

Added:
 * readme_moodle.txt

Changed:
 * Updated H5peditor::getLibraryData parameters to fix PHP8.0 warnings. See MDL-70903 for details.

Downloaded version: moodle-1.20.2 release
