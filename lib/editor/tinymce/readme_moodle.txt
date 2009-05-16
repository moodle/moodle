Description of TinyMCE v3.2.3.1 library import into Moodle

Copyright: (c) 2004-2008, Moxiecode Systems AB, All rights reserved.
License:   GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999


Upgrade procedure:
 1/ Download latest dev package from http://tinymce.moxiecode.com/download.php
 2/ replace tiny_mce*.* files, themes/*, utils/* and plugins/* (keep dragmath, moodlenolink, spellchecker)
 3/ copy tinymce/jscripts/tiny_mce/classes/Popup.js to tiny_mce_popup_src.js
 4/ apply patches
 5/ compress modified js files using yuicompressor.jar from dev package
 6/ download all TinyMCE lang files and update moodle lang string files 

TODO:
 * apply plugins/media/* (extra/tinymce_plugin_media.patch) - MDL-16650
 * lang string handling
 * customize spellchecker
 * finish update with info from http://docs.moodle.org/en/Development:TinyMCE_Upgrade
 * all upgrade info must be here and always kept up-to-date ;-)

=========================================================================================

Removed:
 *

Modified:
 * added tiny_mce_popup_src.js, copy of tinymce/jscripts/tiny_mce/classes/Popup.js from dev package 

Added:
 * plugins/gradmath/*
 * plugins/moodlenolink/*



Petr Skoda (skodak), Mathieu Petit-Clair

$Id$
