<?PHP /*  $Id$ */

/// We use PHP so we can load groups of CSS files
/// because it's so much easier to work with the editor's CSS highlighting

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");
    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);

    include ("./styles_layout.css");
    include ("./styles_typography.css");
    include ("./styles_color.css");
    include ("./styles_moz.css");

?>