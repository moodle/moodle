<?PHP /*  $Id$ */

/// This PHP script is used because it provides a place for setting 
/// up any necessary variables, and lets us include raw CSS files.
/// The output of this script should be a completely standard CSS file.

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");

/// Following lines are just for standard theme/styles.php
    if (!isset($THEME->standardsheets) or $THEME->standardsheets === true) { // Use all the sheets we have
        $subsheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');
    } else if (empty($THEME->standardsheets)) {                              // We can stop right now!
        exit;
    } else {                                                                 // Use the provided subset only
        $subsheets = $THEME->standardsheets;
    }

/// Normal themes will just use a line like this instead of the above.
/// $subsheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');

/// There should be no need to touch the following

    $lastmodified = filemtime('styles.php');

    foreach ($subsheets as $subsheet) {
        $lastmodifiedsub = filemtime($subsheet.'.css');
        if ($lastmodifiedsub > $lastmodified) {
            $lastmodified = $lastmodifiedsub;
        }
    }

    $themeurl = style_sheet_setup($lastmodifiedsub, 600, $themename);

    foreach ($subsheets as $subsheet) {
        include_once($subsheet.'.css');
    }

?>
