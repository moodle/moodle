<?php // $Id$

    require_once("../config.php");

    $preview = optional_param('preview','standard',PARAM_FILE); // which theme to show

    if (!file_exists($CFG->themedir .'/'. $preview)) {
        $preview = 'standard';
    }

    if (! $site = get_site()) {
        error("Site doesn't exist!");
    }

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $CFG->theme = $preview;

    theme_setup($CFG->theme, array('forceconfig='.$CFG->theme));

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strsavechanges = get_string("savechanges");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");

    $navlinks = array();
    $navlinks[] = array('name' => $strthemes, 'link' => null, 'type' => 'misc');
    $navlinks[] = array('name' => $strpreview, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header("$site->shortname: $strpreview", $site->fullname, $navigation);

    print_simple_box_start('center', '80%');
    print_heading($preview);
    print_simple_box_end();

    print_footer();

?>
