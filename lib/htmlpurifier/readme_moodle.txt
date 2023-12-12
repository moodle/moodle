Description of HTML Purifier library import into Moodle

* Make new (or delete contents of) /lib/htmlpurifier/
* Copy everything from /library/ folder to /lib/htmlpurifier/
* Copy CREDITS, LICENSE from root folder to /lib/htmlpurifier/
* Delete unused files:
    HTMLPurifier.auto.php
    HTMLPurifier.autoload.php
    HTMLPurifier.autoload-legacy.php
    HTMLPurifier.composer.php
    HTMLPurifier.func.php
    HTMLPurifier.includes.php
    HTMLPurifier.kses.php
    HTMLPurifier.path.php
* add locallib.php with Moodle specific extensions to /lib/htmlpurifier/
* add this readme_moodle.txt to /lib/htmlpurifier/

Local changes:
* 2023-06-06 Applied patch https://github.com/ezyang/htmlpurifier/pull/346 to avoid PHP 8.2 deprecations.
  See MDL-78143 for more details.