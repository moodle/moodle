<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");
    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);

    include ("./styles_layout.css");
    include ("./styles_font.css");
    include ("./styles_color.css");
    // include ("./styles_block.css");
?>