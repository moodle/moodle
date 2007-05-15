<?php // $Id$

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $choose = optional_param("choose",'',PARAM_FILE);   // set this theme as default

    $adminroot = admin_get_root();
    admin_externalpage_setup('themeselector', $adminroot);

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
              admin_externalpage_print_header($adminroot);
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
              admin_externalpage_print_footer($adminroot);
            exit;
        } else {
            error("Could not set the theme!");
        }
    }

    admin_externalpage_print_header('themeselector');


    print_heading($strthemes);

    $themes = get_list_of_plugins("theme");
    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

    echo "<table style=\"margin-left:auto;margin-right:auto;\" cellpadding=\"7\" cellspacing=\"5\">";
    
    if (!$USER->screenreader) {
        echo "<tr class=\"generaltableheader\"><th scope=\"col\">$strtheme</th>";
        echo "<th scope=\"col\">$strinfo</th></tr>";
    }
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
            $readme =  '<li>'.
            link_to_popup_window($CFG->themewww .'/'. $theme .'/README.html', $theme, $strinfo, 400, 500, '', 'none', true).'</li>';
        } else if (file_exists("$theme/README.txt")) {
            $readme =  '<li>'.
            link_to_popup_window($CFG->themewww .'/'. $theme .'/README.txt', $theme, $strinfo, 400, 500, '', 'none', true).'</li>';
        }
        if (file_exists("$theme/screenshot.png")) {
            $screenshotpath = "$theme/screenshot.png";
        } else if (file_exists("$theme/screenshot.jpg")) {
            $screenshotpath = "$theme/screenshot.jpg";
        }

        echo "<tr>";
             
        // no point showing this if user is using screen reader
        if (!$USER->screenreader) {
            echo "<td align=\"center\">";
            if ($screenshotpath) {
                $screenshot = "<li><a href=\"$theme/screenshot.jpg\">$strscreenshot</a></li>";
                echo "<object type=\"text/html\" data=\"$screenshotpath\" height=\"200\" width=\"400\">$theme</object></td>";
            } else {
                echo "<object type=\"text/html\" data=\"preview.php?preview=$theme\" height=\"200\" width=\"400\">$theme</object></td>";
            }
        }

        if ($CFG->theme == $theme) {
            echo '<td valign="top" style="border-style:solid; border-width:1px; border-color=#555555">';
        } else {
            echo '<td valign="top">';
        }

        if (isset($THEME->sheets)) {
            echo '<p style="font-size:1.5em;font-style:bold;">'.$theme.'</p>';
        } else {
            echo '<p style="font-size:1.5em;font-style:bold;color:red;">'.$theme.' (Moodle 1.4)</p>';
        }
          
        if ($screenshot or $readme) {
            echo '<ul>';      
            if (!$USER->screenreader) {
                echo "<li><a href=\"preview.php?preview=$theme\">$strpreview</a></li>";
            }
            echo $screenshot.$readme;
            echo '</ul>';
        }

        // can not use forms due to object bug in IE :-( see MDL-9799
/*        $options = null;
        $options['choose'] = $theme;
        $options['sesskey'] = $sesskey;
        print_single_('index.php', $options, $strchoose);*/
        echo '<a href="index.php?choose='.$theme.'&amp;sesskey='.sesskey().'">'.$strchoose.'</a>';
        echo '</td>';
        echo "</tr>";
    }
    echo "</table>";


    admin_externalpage_print_footer($adminroot);


?>
