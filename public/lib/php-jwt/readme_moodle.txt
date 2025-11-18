Description of php-jwt library import into Moodle

Dependencies
------------
- The lib/lti1p3 library currently depends on version 6.11.1 of php-jwt.
- There are usages of this library in mod/lti too. Please check these.

Instructions
------------
1. Check dependencies to confirm suitability of the new version of the library (see above).
2. Visit [https://github.com/firebase/php-jwt].
3. Click on 'X releases'.
4. Download the latest release.
5. Remove everything under lib/php-jwt/ except this file (readme_moodle.txt).
6. Unzip the release and put its content as into lib/php-jwt.
7. Update entry for this library in lib/thirdpartylibs.xml.

