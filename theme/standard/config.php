<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control 
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////


$THEME->sheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');

/// This variable is an array containing the names of all the 
/// stylesheet files you want included in this theme, and in what order
////////////////////////////////////////////////////////////////////////////////


$THEME->standardsheets = true;  

/// This variable can be set to an array containing
/// filenames from the *STANDARD* theme.  If the 
/// array exists, it will be used to choose the 
/// files to include in the standard style sheet.
/// When false, then no files are used.
/// When true or NON-EXISTENT, then ALL standard files are used.
/// This parameter can be used, for example, to prevent 
/// having to override too many classes.
/// Note that the trailing .css should not be included
/// eg $THEME->standardsheets = array('styles_layout', 'styles_fonts', 
///                                   'styles_color', 'styles_moz');
////////////////////////////////////////////////////////////////////////////////


$THEME->parent = '';  

/// This variable can be set to the name of a parent theme
/// which you want to have included before the current theme.
/// This can make it easy to make modifications to another 
/// theme without having to actually change the files
/// If this variable is empty or false then a parent theme 
/// is not used.
////////////////////////////////////////////////////////////////////////////////


$THEME->parentsheets = false;  

/// This variable can be set to an array containing
/// filenames from a chosen *PARENT* theme.  If the 
/// array exists, it will be used to choose the 
/// files to include in the standard style sheet.
/// When false, then no files are used.
/// When true or NON-EXISTENT, then ALL standard files are used.
/// This parameter can be used, for example, to prevent 
/// having to override too many classes.
/// Note that the trailing .css should not be included
/// eg $THEME->standardsheets = array('styles_layout', 'styles_fonts', 
///                                   'styles_color', 'styles_moz');
////////////////////////////////////////////////////////////////////////////////


$THEME->custompix = false;

/// If true, then this theme must have a "pix" 
/// subdirectory that contains copies of all 
/// files from the moodle/pix directory, plus a
/// "pix/mod" directory containing all the icons 
/// for all the activity modules.
////////////////////////////////////////////////////////////////////////////////



// These colours are not used anymore, so I've set them to 
// bright green to help identify where they should be removed
// These lines will be deleted soon

$THEME->body         = "#22FF22";  // Main page color
$THEME->cellheading  = "#22FF22";  // Standard headings of big tables
$THEME->cellheading2 = "#22FF22";  // Highlight headings of tables
$THEME->cellcontent  = "#22FF22";  // For areas with text
$THEME->cellcontent2 = "#22FF22";  // Alternate colour
$THEME->borders      = "#22FF22";  // Table borders
$THEME->highlight    = "#22FF22";  // Highlighted text (eg after a search)
$THEME->hidden       = "#22FF22";  // To color things that are hidden
$THEME->autolink     = "#22FF22";  // To color auto-generated links (eg glossary)

?>
