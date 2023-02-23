Description of php-jwt library import into Moodle

Dependencies
------------
- The lib/lti1p3 library currently depends on version 6.0 of php-jwt.
- There are usages of this library in mod/lti too. Please check these.

Instructions
------------
1.  Check dependencies to confirm suitability of the new version of the library (see above).
2.  Visit [https://github.com/firebase/php-jwt].
3.  Click on 'X releases'.
4.  Download the latest release.
5.  Unzip it in lib as php-jwt.
6.  Update entry for this library in lib/thirdpartylibs.xml.

2023/01/26
----------
- src/JWT.php file has minor changes for PHP 8.2 compatibility. See MDL-76415 for more details.
  Since version v6.3.1, the php-jwt already has the fix, so if someone executing the upgrading version and
  it has the patch, please ignore this note.
