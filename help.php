<?PHP
  /// help.php - prints a very simple page and includes a
  ///            page content or a string from elsewhere
  ///            Usually this will appear in a popup 
  ///            See help() in lib/moodlelib.php

  include("config.php");

  if (ereg("\\.\\.", $file)) {
      error("Error: Filenames can not contain \"..\"");
  }


?>
<HTML>
<HEAD>
<LINK rel="stylesheet" href="<?=$CFG->wwwroot?>/theme/<?=$CFG->theme?>/styles.css">
</HEAD>
<BODY BGCOLOR="<?=$THEME->body ?>">
<? if (file_exists("lang/$CFG->lang/page/$file")) {
       include("lang/$CFG->lang/page/$file");
   } else {
       include("lang/en/page/$file");
   }
?>
</BODY>
</HTML>

