Description of import of various YAHOO libraries into Moodle:

1/ YUI2 version 2.8.2:
* full copy of the "build" directory without any changes
* exact version specified in lib/setup.php 

2/ YUI3 version 3.2.0:
* full copy of the "build" directory without any changes
* exact version specified in lib/setup.php

3/ PHPLoader 1.0.0b2:
* removed everything except the lib/meta/* and phploader/loader.php 
* updated meta info from git repo at github
* updated meta info from git repo at github for 3.2.0

NOTE: remove 2.8.1 once most ppl override the old vulnerable files - after next RC

Code downloaded from:
http://developer.yahoo.com/yui
