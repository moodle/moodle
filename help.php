<?PHP
  /// help.php - prints a very simple page and includes a
  ///            page content or a string from elsewhere
  ///            Usually this will appear in a popup 
  ///            See helpbutton() in lib/moodlelib.php

  include("config.php");

  optional_variable($file, "");
  optional_variable($text, "No text to display");
  optional_variable($module, "moodle");

  if (ereg("\\.\\.", $file)) {
      error("Error: Filenames can not contain \"..\"");
  }

?>
<HTML>
<HEAD>
<LINK rel="stylesheet" href="<?=$CFG->wwwroot?>/theme/<?=$CFG->theme?>/styles.css">
</HEAD>
<BODY BGCOLOR="<?=$THEME->body ?>">
<?  if ($file) {
        if ($module == "moodle") {
            $langpath = "$CFG->dirroot/lang";
        } else {
            $langpath = "$CFG->dirroot/mod/$module/lang";
        }

        if (file_exists("$langpath/$CFG->lang/page/$file")) {
            include("$langpath/$CFG->lang/page/$file");
        } else {
            include("$langpath/en/page/$file");
        }
    } else {
        echo "<P>";
        echo $text;
        echo "</P>";
    }
?>
</BODY>
</HTML>

