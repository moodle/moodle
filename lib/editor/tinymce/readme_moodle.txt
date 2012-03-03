Description of TinyMCE v3.4.9 library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2011, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak)

=========================================================================================
Upgrade procedure (by maintainer):

 1/ clone https://github.com/moodle/custom-tinymce
 2/ clone https://github.com/moodle/custom-tinymce_spellchecker_php
 3/ cherry pick Moodle changes into new branches based on stable upstream
 4/ tweak paths in build script in moodle_build.sh and execute
 5/ fix line endings
 6/ download all TinyMCE lang files (extra/tools/download_langs.sh)
 7/ make sure your moodle installation has all language packs installed.
 7/ update moodle lang string files (extra/tools/update_lang_files.php)
 8/ ensure lang packs are updated into AMOS (lang.moodle.net)

=========================================================================================
Prepare local modification procedure (by developer):

 1/ clone https://github.com/moodle/custom-tinymce
 2/ clone https://github.com/moodle/custom-tinymce_spellchecker_php
 3/ apply local modifications to the STABLE branches in those two repos
 4/ tweak paths in build script in moodle_build.sh and execute
 5/ fix line endings
 6/ provide 2 patches into the corresponding MDL issue:
    a) one patch with the version to be applied to custom-tinymce (one for each target branch)
    b) another patch with the resulting changes to be applied to moodle.git (one for each target branch)
 7/ then integrator will:
    a) apply patches in 6a/ to custom-tinymce repo
    b/ standard integration of patches in 6b/ to moodle.git (review, test, upstream)
 8/ Done!

Note1: if the local modification includes lang changes, then steps 6-8 (from upgrade) may be necessary. Contact
AMOS maintainer / custom-tinymce maintainer / integrators about that.

=========================================================================================
Added:
 * plugins/gragmath/*
 * plugins/moodlemotions/*
 * plugins/moodlenolink/*
 * plugins/moodlemedia/*

Modified:
 * image integration - file picker integration
 * string processing - uses our lang framework
 * form hacks
 * MDL-27890 - allow editor to be smaller

 TODO:
 * update strings to integrate with AMOS
