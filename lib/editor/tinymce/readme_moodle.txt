Description of TinyMCE v3.3.9.1 library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2010, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak) 

=========================================================================================
Upgrade procedure:
 1/ clone http://github.com/moodle/tinymce
 2/ clone http://github.com/moodle/tinymce_spellchecker_php
 3/ merge new changes in latest STABLE branches into these two repos
 4/ tweak paths in build script in moodle_build.sh and execute
 5/ fix line endings 
 6/ download all TinyMCE lang files (extra/tools/download_langs.sh)
 7/ make sure your moodle installation has all language packs installed.
 7/ update moodle lang string files (extra/tools/update_lang_files.php)
 8/ ensure lang packs are updated into AMOS (lang.moodle.net)

=========================================================================================
Added:
 * plugins/gragmath/*
 * plugins/moodlenolink/*
 * plugins/moodlemedia/*

Modified:
 * image integration - file picker integration
 * string processing - uses our lang framework
 * form hacks

 TODO:
 * update strings to integrate with AMOS
