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

    $helpfound = false;
  
    if (!empty($file)) {
        $langs = array(current_language(), get_string("parentlanguage"), "en");  // Fallback
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
                break;
            }
        }
    } else {
        echo "<p>";
        echo $text;
        echo "</p>";
        $helpfound = true;
    }

    if (!$helpfound) {
        notify("Help file '$file' could not be found!");
    }

    close_window_button();
?>
</body>
</html>

