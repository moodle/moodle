MongoDB PHP
-----------
Download from https://github.com/mongodb/mongo-php-library/releases

Import procedure:

- Copy all the files and folders from the folder mongodb/src in the cache/stores/mongodb/MongoDB directory.
- Copy the license file from the project root.
- Update thirdpartylibs.xml with the latest version.
- Check the minim php driver version in https://docs.mongodb.com/drivers/php#compatibility and change the
  value in the "are_requirements_met" method if necessary.

This version (1.15.0) requires PHP mongodb extension >= 1.14.0

Local changes:
- Replaced 4 occurrences of get_debug_type() by gettype() to keep PHP 7.4 compatibility. Note this
  has not been applied to 4.2dev and up because, there, it's safe to use get_debug_type().
