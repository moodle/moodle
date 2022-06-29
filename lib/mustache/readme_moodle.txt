Description of Mustache library import into moodle.

1) Download the latest version of mustache.php from upstream (found
at https://github.com/bobthecow/mustache.php/releases)

2) Move the src/ and LICENSE file into lib/mustache

e.g.
wget https://github.com/bobthecow/mustache.php/archive/v2.13.0.zip
unzip v2.13.0.zip
cd mustache.php-2.13.0/
mv src /path/to/moodle/lib/mustache/
mv LICENSE /path/to/moodle/lib/mustache/

Local changes:

Note: All this changes need to be reviewed on every upgrade and, if they have
been already applied upstream for the release being used, can be removed
from the list. If still not available upstream, they will need to be re-applied.

- MDL-67114: PHP 7.4 compatibility. Array operations on scalar value.
  This corresponds to upstream https://github.com/bobthecow/mustache.php/pull/352
- MDL-73586: PHP 8.0 compatibility. Removed 'mbstring.func_overload' init setting.
  This corresponds to upstream commit https://github.com/bobthecow/mustache.php/commit/e7165a33b282ab4d20b3863825caadb46313d62b
  that is availbale for the library versions 2.14.1 and up
