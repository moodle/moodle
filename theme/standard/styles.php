<?PHP /*  $Id$ */

/// This PHP script is used because it provides a place for setting 
/// up any necessary variables, and lets us include raw CSS files.
/// The output of this script should be a completely standard CSS file.

/// There should be no need to modify this file!!  Use config.php instead.

    $nomoodlecookie = true;
    require_once("../../config.php");

    $lastmodified = 0;
    $lifetime = 600;

/// The following lines are only for standard/theme/styles.php

    if (!isset($THEME->standardsheets) or $THEME->standardsheets === true) { // Use all the sheets we have
        $THEME->sheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');
    } else if (empty($THEME->standardsheets)) {                              // We can stop right now!
        exit;
    } else {                                                                 // Use the provided subset only
        $THEME->sheets = $THEME->standardsheets;
    }

/// If we are a parent theme, then check for parent definitions

    if (isset($parent)) {
        if (!isset($THEME->parentsheets) or $THEME->parentsheets === true) {     // Use all the sheets we have
            $THEME->sheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');
        } else if (empty($THEME->parentsheets)) {                                // We can stop right now!
            exit;
        } else {                                                                 // Use the provided subset only
            $THEME->sheets = $THEME->parentsheets;
        }
    }

/// Work out the last modified date for this theme

    foreach ($THEME->sheets as $sheet) {
        $sheetmodified = filemtime($sheet.'.css');
        if ($sheetmodified > $lastmodified) {
            $lastmodified = $sheetmodified;
        }
    }

/// Print out the entire style sheet

    style_sheet_setup($lastmodified, $lifetime);

    foreach ($THEME->sheets as $sheet) {
        echo "/***** $sheet.css start *****/\n\n";
        include_once($sheet.'.css');
        echo "\n\n/***** $sheet.css end *****/\n\n";
    }

?>
