H5P Editor PHP Library
----------------------

Downloaded last release from: https://github.com/h5p/h5p-editor-php-library/releases

Import procedure:
 * Remove the content in this folder (but the readme_moodle.txt)
 * Copy all the files from the folder repository in this directory.

Removed:
 * composer.json
 * .gitignore
 * ckeditor5/sample

Changed:
 * In the method ns.LibrarySelector.prototype.appendTo (scripts/h5peditor-library-selector.js),
   comment the line "this.$selector.appendTo($element);" to avoid the display of the Hub Selector.
 * Review strings in joubel/editor/language/en.js and compare them with
   existing ones in lang/en/h5plib_vXXX.php: add the new ones and remove the
   unexisting ones. Remember to use the AMOS script commands, such CPY, to copy
   all the existing strings from the previous version. As you'll see, all the
   strings in en.js have been converted following these rules:
     - Prefix  "editor:" has been added.
     - Keys have been lowercased.
   Attention: When upgrading to 1.22.4, most of the new strings haven't been added to the lang file
   because they are related to the H5P Hub which is not currently supported by Moodle.
 * Add namespace to this library to avoid collision. It means:
     - Add the "namespace Moodle;" line at the top of all the h5peditor*.php files in the root folder.
     - Replace \H5Pxxx uses to H5Pxxx (for instance, in h5peditor-ajax.class.php there are several references to \H5PCore that
       must be replaced with H5PCore).
 * Add "use stdClass;" in h5peditor.class.php and h5peditor-file.class.php (check that it's still used before replacing it when
   upgrading the library).
 * Edit language/en.js and remove the content for 'filters' (it's a JSON with several fields, such as level or language).
