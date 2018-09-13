# CHANGE HISTORY

### 09 June 2018. 1.0
* Add privacy plugin data
* Increased window width (was 490) to 620 pixels
* Fixed all errors reported by the Moodle code checker plugin: 
  replaced tabs with spaces, 
  removed extra spaces at end of line, 
  added some needed spaces for indentation,
  added some braces in order to fix inline control errors
* Changed version numbering to 3.5r1 to reflect that 
  now it is compatable with Moodle 3.5 branch and that this is minor version 1
 
* (Hopefully) Fixed some phpdocs problems detected in the code by local_moodlecheck
* (Hopefully) Fixed most CSS problems detected by stylelint
 
* Previous version had (212 errors/353 warnings)
  => phplint (0/0), phpcs (0/53), 
  js (187/299), 
  css (9/0), 
  phpdoc (15/0), savepoint (0/0), thirdparty (0/0), 
  grunt (1/1), shifter (0/0), mustache (0/0),

* PHPDocs style problems (15 errors, 0 warnings)
* This section shows the phpdocs problems detected in the code by local_moodlecheck [More info]

* lib/editor/tinymce/plugins/clozeeditor/classes/privacy/provider.php
(#23) File-level phpdocs block is not found
(#25) Class provider is not documented
(#34) Function provider::_get_reason is not documented
(#19) Invalid inline phpdocs tag @package found
(#20) Invalid inline phpdocs tag @copyright found
(#21) Invalid inline phpdocs tag @license found
(#32) Invalid inline phpdocs tag @return found
(#25) Package is not specified for class provider. It is also not specified in file-level phpdocs

* lib/editor/tinymce/plugins/clozeeditor/dialog.php
(#17) File-level phpdocs block is not found
* lib/editor/tinymce/plugins/clozeeditor/lang/en/tinymce_clozeeditor.php
(#16) No one-line description found in phpdocs for file
(#19) Package tiny_mce is not valid
* lib/editor/tinymce/plugins/clozeeditor/lib.php
(#17) File-level phpdocs block is not found
(#32) Function tinymce_clozeeditor::update_init_params is not documented
(#23) Package tiny_mce is not valid
* lib/editor/tinymce/plugins/clozeeditor/version.php
(#19) Package tiny_mce is not valid

* CSS problems (9 errors, 0 warnings)
* This section shows CSS problems detected by stylelint [More info]

* lib/editor/tinymce/plugins/clozeeditor/dialog.css
(#6) Expected single space before "{" (block-opening-brace-space-before)
(#11) Expected a trailing semicolon (declaration-block-trailing-semicolon)
(#2) Expected indentation of 4 spaces (indentation)
(#3) Expected indentation of 4 spaces (indentation)
(#7) Expected indentation of 4 spaces (indentation)
(#15) Expected indentation of 4 spaces (indentation)
(#19) Expected indentation of 4 spaces (indentation)
(#20) Expected indentation of 4 spaces (indentation)
(#19) Unexpected unit "pt" (unit-blacklist)
