<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control 
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////

$THEME->custompix = false;

/// If true, then this theme must have a "pix" 
/// subdirectory that contains copies of all 
/// files from the moodle/pix directory, plus a
/// "pix/mod" directory containing all the icons 
/// for all the activity modules.
////////////////////////////////////////////////////////////////////////////////

$THEME->standardsheets = array('styles_layout', 'styles_fonts', 'styles_color');

/// This variable can be set to an array containing
/// filenames from the *STANDARD* theme.  If the 
/// array exists, it will be used to choose the 
/// files to include in the standard style sheet.
/// When false, then no files are used.
/// When true or NON-EXISTENT, then ALL standard files are used.
/// This parameter can be used, for example, to prevent 
/// having to override too many classes.
/// Note that the trailing '.css' should not be included
/// eg $THEME->standardsheets = array('styles_layout', 'styles_fonts', 
///                                   'styles_color', 'styles_moz');
////////////////////////////////////////////////////////////////////////////////

