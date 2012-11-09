Description of TinyMCE library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2012, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak)

=========================================================================================
Upgrade procedure:

1/ extract standard TinyMCE package into lib/editor/tinymce/tiny_mce/x.y.z/
2/ bump up editor version in lib.php to match the directory name x.y.z
3/ bump up main version.php
4/ update lib/thirdpartylibs.xml
5/ execute cli/update_lang_files.php and review changes in lang/en/editor_tinymce.php
6/ use search to find oversized zIndexes and divide them by 100


Changes:

1/ zIndex 300000 and 200000 changed to 5003 and 5002 - this prevents collision with YUI,
   see MDL-35771

TODO:
 * create some new automated script that sends other languages from upstream into AMOS
