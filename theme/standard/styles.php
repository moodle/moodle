<?PHP /*  $Id$ */

/// This PHP script is used because it provides a place for setting 
/// up any necessary variables, and lets us include raw CSS files.
/// The output of this script should be a completely standard CSS file.


/// These are the stylesheets this theme uses
    $subsheets = array('styles_layout.css', 'styles_fonts.css', 'styles_color.css', 'styles_moz.css');


/// There should be no need to touch the following

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");

    $lastmodified = filemtime('styles.php');

    foreach ($subsheets as $subsheet) {
        $lastmodifiedsub = filemtime($subsheet);
        if ($lastmodifiedsub > $lastmodified) {
            $lastmodified = $lastmodifiedsub;
        }
    }

    $themeurl = style_sheet_setup($lastmodifiedsub, 600, $themename);

    foreach ($subsheets as $subsheet) {
        include_once($subsheet);
    }

?>
