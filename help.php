<?PHP /// $Id$
      /// help.php - prints a very simple page and includes a
      ///            page content or a string from elsewhere
      ///            Usually this will appear in a popup 
      ///            See helpbutton() in lib/moodlelib.php

    require_once("config.php");

    optional_variable($file, "");
    optional_variable($text, "No text to display");
    optional_variable($module, "moodle");

    print_header();

    if (detect_munged_arguments("$module/$file")) {
        error("Filenames contain illegal characters!");
    }

    print_simple_box_start("center", "96%");

    $helpfound = false;
    $langs = array(current_language(), get_string("parentlanguage"), "en");  // Fallback

    if (!empty($file)) {
        foreach ($langs as $lang) {
            if (empty($lang)) {
                continue;
            }
            if ($module == "moodle") {
                $filepath = "$CFG->dirroot/lang/$lang/help/$file";
            } else {
                $filepath = "$CFG->dirroot/lang/$lang/help/$module/$file";
            }
  
            if (file_exists("$filepath")) {
                $helpfound = true;
                include("$filepath");   // The actual helpfile

                if ($module == "moodle" and ($file == "index.html" or $file == "mods.html")) {
                    // include file for each module

                    if (!$modules = get_records("modules", "visible", 1)) {
                        error("No modules found!!");        // Should never happen
                    }

                    foreach ($modules as $mod) {
                        $strmodulename = get_string("modulename", "$mod->name");
                        $modulebyname[$strmodulename] = $mod;
                    }
                    ksort($modulebyname);

                    foreach ($modulebyname as $mod) {
                        foreach ($langs as $lang) {
                            if (empty($lang)) {
                                continue;
                            }
                            $filepath = "$CFG->dirroot/lang/$lang/help/$mod->name/$file";

                            if (file_exists("$filepath")) {
                                echo '<hr size="1" />';
                                include("$filepath");   // The actual helpfile
                                break;
                            }
                        }
                    }
                }

                if ($module == "moodle" and ($file == "resource/types.html")) {  // RESOURCES
                    require_once("$CFG->dirroot/mod/resource/lib.php");
                    $typelist = resource_get_resource_types();
                    $typelist['label'] = get_string('resourcetypelabel', 'resource');

                    foreach ($typelist as $type => $name) {
                        foreach ($langs as $lang) {
                            if (empty($lang)) {
                                continue;
                            }
                            $filepath = "$CFG->dirroot/lang/$lang/help/resource/type/$type.html";
                            if (file_exists("$filepath")) {
                                echo '<hr size="1" />';
                                include("$filepath");   // The actual helpfile
                                break;
                            }
                        }
                    }
                }
                break;
            }
        }
    } else {
        echo "<p>";
        echo clean_text($text);
        echo "</p>";
        $helpfound = true;
    }

    print_simple_box_end();

    if (!$helpfound) {
        $file = clean_text($file);  // Keep it clean!
        notify("Help file '$file' could not be found!");
    }

    close_window_button();

    echo "<center><p><a href=\"help.php?file=index.html\">".get_string("helpindex")."</a><p></center>";
?>
</body>
</html>

