H5P PHP library
---------------

Downloaded last release from:  https://github.com/h5p/h5p-php-library/tags

When no new tags are released, a specific commit can also be used. In that case, the version number used in the thirdpartylibs.xml
will be <branch name>-<commit hash>. For instance, master-f3579c0.

Import procedure:
 * Remove the content in this folder (but the readme_moodle.txt)
 * Copy all the files from the folder repository in this directory.

Removed:
 * composer.json
 * .gitignore
 * .travis.yml

Changed:
 0. Open the new version of joubel/core/h5p.classes.php and at the beginning of the H5PCore class (around line 2082), check the
 value of the coreApi minor and major versions. If they are different from the values in the current Moodle version, instead of
 upgrading the library in the current h5plib_vxxx, a new h5plib_vX.Y should be released (as it was done in MDL-80544). The new
 h5plib module should be named taking into account that X is the coreApi.majorVersion and Y is the coreApi.minorVersion.

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

4. In saveLibraries() method in core/h5p.classes.php, check $this->h5pF->saveLibraryData is called before $this->h5pC->fs->saveLibrary.
The library needs to be saved in the database first before creating the files, because the libraryid is used as itemid for the files.

5. Check if new methods have been added to any of the interfaces. If that's the case, implement them in the proper class. For
instance, if a new method is added to h5p-file-storage.interface.php, it should be implemented in h5p/classes/file_storage.php.

6. Open js/h5p.js and in function contentUserDataAjax() add the following patch:
  function contentUserDataAjax(contentId, dataType, subContentId, done, data, preload, invalidate, async) {
    if (H5PIntegration.user === undefined) {
      // Not logged in, no use in saving.
      done('Not signed in.');
      return;
    }
    // Moodle patch to let override this method.
    if (H5P.contentUserDataAjax !== undefined) {
      return H5P.contentUserDataAjax(contentId, dataType, subContentId, done, data, preload, invalidate, async);
    }
    // End of Moodle patch.

    var options = {
