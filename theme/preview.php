<?php // $Id$

    require_once("../config.php");

    $preview = optional_param('preview','standard',PARAM_FILE); // which theme to show

    if (!file_exists($preview)) {
        $preview = 'standard';
    }

    if (! $site = get_site()) {
        error("Site doesn't exist!");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to change themes.");
    }

    $CFG->theme       = $preview;
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";

    print_header();
    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strsavechanges = get_string("savechanges");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");

    print_header("$site->shortname: $strpreview", $site->fullname, "$strthemes -> $strpreview");

    print_simple_box_start('center', '80%');
    print_heading($preview);
    print_simple_box_end();

    print_footer();

?>
