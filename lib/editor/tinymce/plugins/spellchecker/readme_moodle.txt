This is a modification of php spellchecker plugin by Moxiecode Systems AB,
see https://github.com/tinymce/tinymce_spellchecker_php

List of changes:
* Add support for curl proxy when accessing Google spell service.
* Workaround for error() function collisions.
* Modified config file to use moodle $CFG.
* Moved static files to /tinymce/ subfolder.
* MDL-25736 - French spellchecker fixes.
* Fix htmlentities conversion in GoogleSpell.php
