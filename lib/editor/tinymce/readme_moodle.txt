Description of TinyMCE v3.2.5 library integration in Moodle
=========================================================================================

Copyright: (c) 2004-2008, Moxiecode Systems AB, All rights reserved.
License: GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999

Moodle maintainer: Petr Skoda (skodak)

=========================================================================================
Upgrade procedure:
 1/ Download latest dev package from http://tinymce.moxiecode.com/download.php
 2/ replace tiny_mce*.* files, themes/*, utils/* and plugins/*
    (keep dragmath, moodlenolink, spellchecker)
 3/ copy tinymce/jscripts/tiny_mce/classes/Popup.js to Popup.js
 4/ apply strings & form_utils.js & tinymce_plugin_advimage patches
 5/ copy yuicompressor.jar from dev package into extra/tools/, use shell
    script extra/tools/compress.sh to compress modified files
 6/ download all TinyMCE lang files (extra/tools/download_langs.sh)
 7/ update moodle lang string files (extra/tools/update_lang_files.php)
 8/ apply tinymce_lang_string.patch which contains language string for moodlemedia plugin

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
