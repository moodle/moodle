<?PHP // $Id$

    require_once("../config.php");

    optional_variable($preview);   // which theme to show
    optional_variable($choose);    // set this theme as default

    if (! $site = get_site()) {
        error("Site doesn't exist!");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to change themes.");
    }

    if ($choose) {
        if (!is_dir($choose)) {
            error("This theme is not installed!");
        }
        $preview = $choose;
    }

    if ($preview) {
        $CFG->theme = $preview;
        $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.php?themename=$preview";
        $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
        $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";
        include ("$CFG->theme/config.php");
    }

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strsavechanges = get_string("savechanges");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");

    print_header("$site->shortname: $strthemes", $site->fullname, 
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> $strthemes");

    if ($choose) {
        if (set_config("theme", $choose)) {
            print_heading(get_string("themesaved"));
            print_continue("$CFG->wwwroot");

            if (file_exists("$choose/README.html")) {
                print_simple_box_start("center");
                readfile("$choose/README.html");
                print_simple_box_end();

            } else if (file_exists("$choose/README.txt")) {
                print_simple_box_start("center");
                $file = file("$choose/README.txt");
                echo format_text(implode('', $file), FORMAT_MOODLE);
                print_simple_box_end();
            }
            print_footer();
            exit;
        } else {
            error("Could not set the theme!");
        }
    }

    print_heading(get_string("previeworchoose"));

    $themes = get_list_of_plugins("theme");

    echo "<TABLE ALIGN=CENTER cellpadding=7 cellspacing=5>";
    echo "<TR><TH class=\"generaltableheader\">$strtheme<TH class=\"generaltableheader\">&nbsp;</TR>";
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
        echo "</tr>";
    }
    echo "</table>";

    echo "<br><div align=center>";
    $options["frame"] = "developer.html";
    $options["sub"] = "themes";
    print_single_button("$CFG->wwwroot/doc/index.php", $options, get_string("howtomakethemes"));
    echo "</div>";
    print_footer();

?>
