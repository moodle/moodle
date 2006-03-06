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

Special usage
=============

In some rare cases programmer needs to force certain settings. If you don't want to 
take care of both editor's settings you can force your module to use one editor only.
In that case you'll have to pass an associative array as an argument 
for loadeditor function:


    $args = array('courseid' => $course->id, 'name' => 'tinymce');
    $editor = loadeditor($args);
    
Then you can define settings for the editor that you wish to use. For setting up new
settings use setconfig() method:
	
    Tiny example1:

    $editor->setconfig('mode','exact');
    $editor->setconfig('elements','mytextarea');
    $editor->setconfig('plugins','advhr,table,flash');
    
    // Merge config to defaults and startup the editor.
    $editor->starteditor('merge');

    Tiny example2:
    
    $args['mode'] = 'exact';
    $args['elements'] = 'mytextarea';

    $args['plugins'] = 'advhr,table,flash';

    // merge config to defaults and startup the editor.
    $editor->starteditor('merge');

    HTMLArea example1:
	
    $toolbar = array(
                array("fontname","fontsize","separator","undo","redo"),
                array("cut","copy","paste","separator","fullscreen")
               );
    $editor->setconfig('toolbar', $toolbar);
    $editor->setconfig('killWordOnPaste', false);
    $editor->starteditor('merge');

    HTMLArea example2:

    $args['toolbar'] = array(
                           array("fontname","fontsize","separator","undo","redo"),
                           array("cut","copy","paste","separator","fullscreen")
                       );
    $args['killWordOnPaste'] = false;
    $args['pageStyle'] = "body { font-family: Verdana; font-size: 10pt; }";

    $editor->setconfig($args);

    // Print only these settings and start up the editor.
    $editor->starteditor();

There are three possible arguments for starteditor method. Which are:
append, merge and default.

 append: Leave default values untouched if overlapping settings are found.
  merge: Override default values if same configuration settings are found. 
default: Use only default settings.

If none of these options is present then only those settings are used what
you've set with setsetting method.



TinyMCE configuration options
=============================

You can find full list of configuration options and possible values 
at http://tinymce.moxiecode.com/tinymce/docs/reference_configuration.html

HTMLArea configuration options
==============================

Possible configuration options for HTMLArea are:

 width (string)
 **************
 Width of the editor as a string. Example: "100%" or "250px".

 height (string)
 ***************
 Height of the editor as a string. Example: "100%" or "150px".

 statusBar (boolean)
 *******************
 Print out statusbar or not. Example: true or false.

 undoSteps (integer)
 *******************
 Amount of undo steps to hold in memory. Default is 20.

 undoTimeout (integer)
 *********************
 The time interval at which undo samples are taken. Default 500 (1/2 sec).

 sizeIncludesToolbar (boolean)
 *****************************
 Specifies whether the toolbar should be included in the size or not.
 Default is true.

 fullPage (boolean)
 ******************
 If true then HTMLArea will retrieve the full HTML, starting with the
 <HTML> tag. Default is false.

 pageStyle (string)
 ******************
 Style included in the iframe document.
 Example: "body { background-color: #fff; font-family: 'Times New Roman', Times; }".

 killWordOnPaste (boolean)
 *************************
 Set to true if you want Word code to be cleaned upon Paste. Default is true.

 toolbar (array of arrays)
 *************************
 Buttons to print in toolbar. Must be array of arrays.
 Example: array(array("Fontname","fontsize"), array("cut","copy","paste"));
 Will print toolbar with two rows.
 
 fontname (associative array)
 ****************************
 Fontlist for fontname drowdown list.
 Example: array("Arial" => "arial, sans-serif", "Tahoma", "tahoma,sans-serif");

 fontsize (associative array)
 ****************************
 Fontsizes for fontsize dropdown list.
 Example: array("1 (8pt)" => "1", "2 (10pt)" => "2");

 formatblock (associative array)
 *******************************
 An associative array of formatting options for formatblock dropdown list.
 Example: array("Heading 1" => "h1", "Heading 2" => "h2");


To be continue...
    
$Id$