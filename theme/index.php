<?PHP // $Id$

    require("../config.php");

    optional_variable($preview);   // which theme to show
    optional_variable($choose);    // set this theme as default

    if ($choose) {
        $preview = $choose;
    }

    if ($preview) {
        $CFG->theme = $preview;
        $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.css";
        $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
        $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";
        include ("$CFG->theme/config.php");
    }


    if (! $site = get_site()) {
        error("Site doesn't exist!");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to change themes.");
    }


    $stradministration = get_string("administration");
    $strchoosetheme = get_string("choosetheme");
    $strpreview = get_string("preview");
    $strsavechanges = get_string("savechanges");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");

    print_header("$site->fullname : $strchoosetheme", $site->fullname, 
                 "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> -> $strchoosetheme");

    if ($choose) {
        if (set_config("theme", $choose)) {
            print_heading(get_string("themesaved"));
            print_continue("$CFG->wwwroot");
            print_footer();
            exit;
        } else {
            error("Could not set the theme!");
        }
    }

    print_heading(get_string("previeworchoose"));

    $themes = get_list_of_plugins("theme");

    echo "<TABLE ALIGN=CENTER cellpadding=7 cellspacing=5>";
    echo "<TR><TH>$strtheme<TH>&nbsp;</TR>";
    foreach ($themes as $theme) {
        include ("$theme/config.php");
        echo "<TR>";
        if ($CFG->theme == $theme) {
            echo "<TD ALIGN=CENTER BGCOLOR=\"$THEME->body\">$theme</TD>";
            echo "<TD ALIGN=CENTER><A HREF=\"index.php?choose=$theme\">$strsavechanges</A></TD>";
        } else {
            echo "<TD ALIGN=CENTER BGCOLOR=\"$THEME->body\">";
            echo "<A TITLE=\"$strpreview\" HREF=\"index.php?preview=$theme\">$theme</A>";
            echo "</TD>";
            echo "<TD>&nbsp;</TD>";
        }
        echo "</TR>";
    }
    echo "</TABLE>";

    print_footer();

?>
