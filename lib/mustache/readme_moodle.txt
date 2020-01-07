Description of Mustache library import into moodle.

1) Download the latest version of mustache.php from upstream (found
at https://github.com/bobthecow/mustache.php/releases)

2) Move the src/ and LICENSE file into lib/mustache

e.g.
wget https://github.com/bobthecow/mustache.php/archive/v2.12.0.zip
unzip v2.12.0.zip
cd mustache.php-2.12.0/
mv src /path/to/moodle/lib/mustache/
mv LICENSE /path/to/moodle/lib/mustache/

Local changes:

Note: All this changes need to be reviewed on every upgrade and, if they have
been already applied upstream for the release being used, can be removed
from the list. If still not available upstream, they will need to be re-applied.

- MDL-67114: PHP 7.4 compatibility. Array operations on scalar value.
  This corresponds to upstream https://github.com/bobthecow/mustache.php/pull/352
