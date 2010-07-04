Description of TinyMCE v3.3.8 library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2010, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak)

=========================================================================================
Upgrade procedure:
 1/ Download latest dev package from http://tinymce.moxiecode.com/download.php
 2/ copy TinyMCE files to tiny_mce/version/
 3/ copy extra dragmath, moodlenolink, spellchecker, moodlemedia
 4/ copy tinymce/jscripts/tiny_mce/classes/Popup.js to Popup.js
 5/ apply strings & form_utils.js & tinymce_plugin_advimage patches
 6/ copy yuicompressor.jar from dev package into extra/tools/, use shell
    script extra/tools/compress.sh to compress modified files
 7/ download all TinyMCE lang files (extra/tools/download_langs.sh)
 8/ update moodle lang string files (extra/tools/update_lang_files.php)

=========================================================================================
Added:
 * added Popup.js, copy of tinymce/jscripts/tiny_mce/classes/Popup.js from dev package 
 * plugins/gragmath/*
 * plugins/moodlenolink/*
 * plugins/moodlemedia/*
 * plugins/spellchecker/*

Modified:
 * Popup.js --> compressed into tiny_mce_popup.js (extra/patches/tinymce_strings.patch)
 * tiny_mce_src.js --> compressed into tiny_mce.js (extra/patches/tinymce_strings.patch)

Removed:
 *

 TODO:
 * update strings to integrate with AMOS
