<?PHP
  /// help.php - prints a very simple page and includes a
  ///            page content or a string from elsewhere
  ///            Usually this will appear in a popup 
  ///            See helpbutton() in lib/moodlelib.php

  require_once("config.php");

  optional_variable($file, "");
  optional_variable($text, "No text to display");
  optional_variable($module, "moodle");

  $lang = current_language();

  print_header();

  if (detect_munged_arguments("$module/$file")) {
      error("Filenames contain illegal characters!");
  }

  if ($file) {
        if ($module == "moodle") {
            $filepath = "$CFG->dirroot/lang/$lang/help/$file";
        } else {
            $filepath = "$CFG->dirroot/lang/$lang/help/$module/$file";
        }

        if (file_exists("$filepath")) {
            require_once("$filepath");           // Chosen language

        } else {                                 // Fall back to English
            if ($module == "moodle") {
                $filepath = "$CFG->dirroot/lang/en/help/$file";
            } else {
                $filepath = "$CFG->dirroot/lang/en/help/$module/$file";
            }

            if (file_exists("$filepath")) {
                require_once("$filepath");
            } else {
                notify("Can not find the specified help file");
                die;
            }
        }
    } else {
        echo "<p>";
        echo $text;
        echo "</p>";
    }

    close_window_button();
?>
</body>
</html>

