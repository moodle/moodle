<?PHP
  /// help.php - prints a very simple page and includes a
  ///            page content or a string from elsewhere
  ///            Usually this will appear in a popup 
  ///            See helpbutton() in lib/moodlelib.php

  include("config.php");

  optional_variable($file, "");
  optional_variable($text, "No text to display");
  optional_variable($module, "moodle");

  $lang = current_language();

  print_header();

  if (ereg("\\.\\.", $file)) {
      error("Filenames can not contain \"..\"");
  }

  if ($file) {
        if ($module == "moodle") {
            $filepath = "$CFG->dirroot/lang/$lang/help/$file";
        } else {
            $filepath = "$CFG->dirroot/lang/$lang/help/$module/$file";
        }

        if (file_exists("$filepath")) {
            include("$filepath");           // Chosen language

        } else {                            // Fall back to English
            if ($module == "moodle") {
                $filepath = "$CFG->dirroot/lang/en/help/$file";
            } else {
                $filepath = "$CFG->dirroot/lang/en/help/$module/$file";
            }

            if (file_exists("$filepath")) {
                include("$filepath");
            } else {
                notify("Can not find the specified help file");
                die;
            }
        }
    } else {
        echo "<P>";
        echo $text;
        echo "</P>";
    }

    close_window_button();
?>
</BODY>
</HTML>

