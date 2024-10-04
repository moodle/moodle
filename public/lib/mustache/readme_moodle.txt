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

1) If the relevant pull request has not been accepted yet, apply the following commit, so we are able to disable unnecessary rendering:
https://github.com/bobthecow/mustache.php/pull/402/commits/db771014c7e346438f68077813ebdda3fdae12df#
This can be achieved by:
    a) Download the patch from
    https://github.com/bobthecow/mustache.php/pull/402/commits/db771014c7e346438f68077813ebdda3fdae12df.patch
    b) In terminal, navigate to lib/mustache/src/Mustache
    c) Run the following: patch --directory . < ~/path/to/patch.patch
    d) We do not need the unit test, so run rm DisableLambdaRenderingTest.php
2) Apply local changes to ensure that all nullable method parameters are correctly type-hinted.
   These can be detected using:
   phpcs --sniffs=PHPCompatibility.FunctionDeclarations.RemovedImplicitlyNullableParam lib/mustache
