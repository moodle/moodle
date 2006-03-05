editorObject README
===================

Quick specs
===========

Quick specs for new editor intergration class.
This new intergration method lets user to choose which editor to use if
user chooses to use WYSIWYG editor (HTMLArea or TinyMCE).

There are legacy code for backward compatibilty in case that modules are not
upgraded for both editors. In such case only HTMLArea is available.
Structure implemented with factory design pattern:

    * /lib
          o editorlib.php
    * /lib/editor
          o htmlarea
                + htmlarea.class.php
          o tinymce
                + tinymce.class.php

Usage:

Editor scripts must be loaded before print_header() function call and
only required variable is course id. To load editor you can use wrapper
function located in moodlelib.php called loadeditor().

    if ( $usehtmleditor = can_use_html_editor() ) {
        $editor = loadeditor($course->id);
    }

This will push needed scripts to global $CFG->editorsrc array which will be
printed out in /lib/javascript.php.
And at the bottom of the page before print_footer() function,
we'll startup the editor almost as usual:

    if ( $usehtmleditor ) {
        $editor->use_html_editor();
    }

After $editor->use_html_editor() -method is called $CFG->editorsrc array is cleared,
so these scripts are loaded only when necessary.

$Id$