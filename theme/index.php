<?php // $Id$

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $choose = optional_param("choose",'',PARAM_FILE);   // set this theme as default

    admin_externalpage_setup('themeselector');

    unset($SESSION->theme);

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strchoose = get_string("choose");
    $strinfo = get_string("info");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");
    $strscreenshot = get_string("screenshot");
    $stroldtheme = get_string("oldtheme");


    if ($choose and confirm_sesskey()) {
        if (!is_dir($CFG->themedir .'/'. $choose)) {
            error("This theme is not installed!");
        }
        if (set_config("theme", $choose)) {
            theme_setup($choose);
            admin_externalpage_print_header();
            print_heading(get_string("themesaved"));

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
            
            print_continue("$CFG->wwwroot/");
            
            admin_externalpage_print_footer();
            exit;
        } else {
            error("Could not set the theme!");
        }
    }

    admin_externalpage_print_header('themeselector');


    print_heading($strthemes);

    $themes = get_list_of_plugins("theme");
    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

    echo "<table style=\"margin-left:auto;margin-right:auto;\" cellpadding=\"7\" cellspacing=\"5\">\n";

    if (!$USER->screenreader) {
        echo "\t<tr class=\"generaltableheader\">\n\t\t<th scope=\"col\">$strtheme</th>\n";
        echo "\t\t<th scope=\"col\">$strinfo</th>\n\t</tr>\n";
    }

    $original_theme = fullclone($THEME);

    foreach ($themes as $theme) {

        unset($THEME);

        if (!file_exists($CFG->themedir.'/'.$theme.'/config.php')) {   // bad folder
            continue;
        }

        include($CFG->themedir.'/'.$theme.'/config.php');

        $readme = '';
        $screenshot = '';
        $screenshotpath = '';

        if (file_exists("$theme/README.html")) {
            $readme =  "\t\t\t\t<li>".
            link_to_popup_window($CFG->themewww .'/'. $theme .'/README.html', $theme, $strinfo, 400, 500, '', 'none', true)."</li>\n";
        } else if (file_exists("$theme/README.txt")) {
            $readme =  "\t\t\t\t<li>".
            link_to_popup_window($CFG->themewww .'/'. $theme .'/README.txt', $theme, $strinfo, 400, 500, '', 'none', true)."</li>\n";
        }
        if (file_exists("$theme/screenshot.png")) {
            $screenshotpath = "$theme/screenshot.png";
        } else if (file_exists("$theme/screenshot.jpg")) {
            $screenshotpath = "$theme/screenshot.jpg";
        }

        echo "\t<tr>\n";

        // no point showing this if user is using screen reader
        if (!$USER->screenreader) {
            echo "\t\t<td align=\"center\">\n";
            if ($screenshotpath) {
                $screenshot = "\t\t\t\t<li><a href=\"$theme/screenshot.jpg\">$strscreenshot</a></li>\n";
                echo "\t\t\t<object type=\"text/html\" data=\"$screenshotpath\" height=\"200\" width=\"400\">$theme</object>\n\t\t</td>\n";
            } else {
                echo "\t\t\t<object type=\"text/html\" data=\"preview.php?preview=$theme\" height=\"200\" width=\"400\">$theme</object>\n\t\t</td>\n";
            }
        }

        if ($CFG->theme == $theme) {
            echo "\t\t" . '<td valign="top" style="border-style:solid; border-width:1px; border-color:#555555">'."\n";
        } else {
            echo "\t\t" . '<td valign="top">'."\n";
        }

        if (isset($THEME->sheets)) {
            echo "\t\t\t" . '<p style="font-size:1.5em;font-weight:bold;">'.$theme.'</p>'."\n";
        } else {
            echo "\t\t\t" . '<p style="font-size:1.5em;font-weight:bold;color:red;">'.$theme.' (Moodle 1.4)</p>'."\n";
        }

        if ($screenshot or $readme) {
            echo "\t\t\t<ul>\n";
            if (!$USER->screenreader) {
                echo "\t\t\t\t<li><a href=\"preview.php?preview=$theme\">$strpreview</a></li>\n";
            }
            echo $screenshot.$readme;
            echo "\t\t\t</ul>\n";
        }

        $options = null;
        $options['choose'] = $theme;
        $options['sesskey'] = $sesskey;
        echo "\t\t\t" . print_single_button('index.php', $options, $strchoose, 'get', null, true) . "\n";
        echo "\t\t</td>\n";
        echo "\t</tr>\n";
    }
    echo "</table>\n";

    $THEME = $original_theme;

    admin_externalpage_print_footer();
?>
