<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles
    $nomoodlecookie = true;

    require_once("../../config.php");
    $themename = optional_param('themename', NULL, PARAM_SAFEDIR);

    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);

///
/// You can hardcode colours in this file if you
/// don't care about this.

?>
