<?PHP /*  $Id$ */

/// This PHP script is used because it provides a place for setting 
/// up any necessary variables, and lets us include raw CSS files.
/// The output of this script should be a completely standard CSS file.

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");

    $themeurl = style_sheet_setup(filemtime("styles.php"), 600, $themename);

    include('styles_layout.css');
    include('styles_fonts.css');
    include('styles_color.css');
    include('styles_moz.css');

?>
