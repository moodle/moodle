<?PHP /*  $Id$ */

/// This PHP script is used because it provides a place for setting 
/// up any necessary variables, and lets us include raw CSS files.
/// The output of this script should be a completely standard CSS file.

/// THERE IS USUALLY NO NEED TO EDIT THIS FILE!  See config.php

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");

    $lastmodified = filemtime('styles.php');

    foreach ($THEME->subsheets as $subsheet) {
        $lastmodifiedsub = filemtime($subsheet.'.css');
        if ($lastmodifiedsub > $lastmodified) {
            $lastmodified = $lastmodifiedsub;
        }
    }

    $themeurl = style_sheet_setup($lastmodifiedsub, 600, $themename);

    foreach ($THEME->subsheets as $subsheet) {
        include_once($subsheet.'.css');
    }

?>
