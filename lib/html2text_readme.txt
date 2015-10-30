Customised html2text.php from the RoundCube project:

  http://trac.roundcube.net/log/trunk/roundcubemail/program/lib/html2text.php

 -- Francois Marier <francois@catalyst.net.nz>  2009-05-22

Modifications
--------------

1. Don't just strip images, replace them with their alt text. (Tim Hunt 2010-08-04)
2. No strip slashes, we do not use magic quotes any more in Moodle 2.0 or later
3. Use core_text, not crappy functions that break UTF-8, in the _strtoupper method. (Tim Hunt 2010-11-02)
4. Make sure html2text does not destroy '0'. (Tim Hunt 2011-09-21)
5. define missing mail charset
6. Fixed the links list enumeration (MDL-35206).


Imported from: https://github.com/moodle/custom-html2text/tree/MOODLE_5886_1
