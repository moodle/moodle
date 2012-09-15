Description of TinyMCE library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2012, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak)

=========================================================================================
Upgrade procedure (by maintainer):

 1/ clone https://github.com/moodle/custom-tinymce
 2/ cherry pick Moodle changes into new branches based on stable upstream
 3/ tweak paths in build script in moodle_build.sh and execute
 4/ fix line endings
 5/ download all TinyMCE lang files (extra/tools/download_langs.sh)
 6/ make sure your moodle installation has all language packs installed.
 7/ update moodle lang string files (extra/tools/update_lang_files.php)
 8/ ensure lang packs are updated into AMOS (lang.moodle.net)

=========================================================================================
Prepare local modification procedure (by developer):

 1/ clone https://github.com/moodle/custom-tinymce
 2/ apply local modifications to the STABLE branches in those two repos
 3/ tweak paths in build script in moodle_build.sh and execute
 4/ fix line endings
 5/ provide 2 patches into the corresponding MDL issue:
    a) one patch with the version to be applied to custom-tinymce (one for each target branch)
    b) another patch with the resulting changes to be applied to moodle.git (one for each target branch)
 6/ then integrator will:
    a) apply patches in 5a/ to custom-tinymce repo
    b/ standard integration of patches in 5b/ to moodle.git (review, test, upstream)
 7/ Done!

Note1: if the local modification includes lang changes, then steps 5-7 (from upgrade) may be necessary.
Contact AMOS maintainer / custom-tinymce maintainer / integrators about that.

=========================================================================================

Modified:
 * string processing - uses our lang framework instead of js files

TODO:
 * update strings to integrate with AMOS
