<?php // $Id$

    require_once("../config.php");

    $choose = optional_param("choose",'',PARAM_FILE);   // set this theme as default

    if (! $site = get_site()) {
        error("Site doesn't exist!");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to change themes.");
    }

    unset($SESSION->theme);

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strchoose = get_string("choose");
    $strinfo = get_string("info");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");


    if ($choose and confirm_sesskey()) {
        if (!is_dir($choose)) {
            error("This theme is not installed!");
        }
        if (set_config("theme", $choose)) {
            theme_setup($choose);

            print_header("$site->shortname: $strthemes", $site->fullname, 
                 "<a href=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/$CFG->admin/configure.php\">$strconfiguration</a> -> $strthemes");
            print_heading(get_string("themesaved"));
            print_continue("$CFG->wwwroot/");

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

    print_header("$site->shortname: $strthemes", $site->fullname, 
                 "<a href=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/$CFG->admin/configure.php\">$strconfiguration</a> -> $strthemes");


    print_heading($strthemes);

    $themes = get_list_of_plugins("theme");
    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

    echo "<table align=\"center\" cellpadding=\"7\" cellspacing=\"5\">";
    echo "<tr class=\"generaltableheader\"><th>$strtheme</th><th>$strinfo</th></tr>";
    foreach ($themes as $theme) {

        if (!file_exists("$CFG->dirroot/theme/$theme/config.php")) {   // bad folder
            continue;
        }

        unset($THEME);
        include_once("$CFG->dirroot/theme/$theme/config.php");

        echo "<tr>";
        echo "<td align=\"center\"><iframe name=\"$theme\" src=\"preview.php?preview=$theme\" height=\"150\" width=\"500\"></iframe></td>";

        if ($CFG->theme == $theme) {
            echo '<td valign="top" style="border-style:solid; border-width:2px; border-color=#000000">';
        } else {
            echo '<td valign="top">';
        }
        echo "<h4>$theme</h4>";
        if (!isset($THEME->sheets)) {
            notify("OLD THEME!!");
        }

        echo '<ul>';

        if (file_exists("$theme/README.html")) {
            echo "<li><a target=\"$theme\" href=\"preview.php?preview=$theme\">$strpreview</a>";
            echo '<li>';
            link_to_popup_window('/theme/'.$theme.'/README.html', $theme, $strinfo);
        } else if (file_exists("$theme/README.txt")) {
            echo "<li><a target=\"$theme\" href=\"preview.php?preview=$theme\">$strpreview</a>";
            echo '<li>';
            link_to_popup_window('/theme/'.$theme.'/README.txt', $theme, $strinfo);
        }
        if ($CFG->theme != $theme) {
            echo "<li><a href=\"index.php?choose=$theme&amp;sesskey=$sesskey\">$strchoose</a>";
        }
        echo '</ul>';
        echo '</td>';
        echo "</tr>";
    }
    echo "</table>";

    echo "<br /><div align=\"center\">";
    $options["frame"] = "developer.html";
    $options["sub"] = "themes";
    print_single_button("$CFG->wwwroot/doc/index.php", $options, get_string("howtomakethemes"));
    echo "</div>";
    print_footer();

?>
