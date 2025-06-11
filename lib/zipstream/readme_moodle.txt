Instructions to import ZipStream into Moodle:

1/ Download from https://github.com/maennchen/ZipStream-PHP/releases/

2/ Copy the LICENSE file and the src folder into the lib/zipstream folder

3/ Ensure any dependencies required are also imported, e.g.:
   - php-64bit
   - ext-mbstring
   - ext-zlib
   The dependencies will be listed in the "require" section of
   the library's composer.json (https://github.com/maennchen/ZipStream-PHP/blob/<X.Y.Z>/composer.json).
   X.Y.Z is a version number.
