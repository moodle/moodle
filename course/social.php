<?  // $Id$
    // social.php - course format featuring social forum
    //              included from view.php
    
    include("../mod/forum/lib.php");
    include("../mod/reading/lib.php"); 
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD WIDTH="15%" VALIGN="TOP">
      <? 
      //if ($news = forum_get_course_forum($course->id, "news")) {
          //forum_print_latest_discussions($news->id, 5, "minimal", "DESC", false);
      //}

      //echo "<BR><BR>";
      print_simple_box("People", $align="CENTER", $width="100%", $color="$THEME->cellheading");
      $moddata[]="<A HREF=\"../user/index.php?id=$course->id\">List of all people</A>";
      $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"List of everyone\">";
      $moddata[]="<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">Edit my profile</A>";
      $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"Me\">";
      print_side_block("", $moddata, "", $modicon);
      

      // Then, print all the available readings

      print_simple_box("Resources", $align="CENTER", $width="100%", $color="$THEME->cellheading");

      if ($readings = reading_list_all_readings($course->id, "timemodified DESC", 0, true)) {
          foreach ($readings as $reading) {
              $readingdata[] = $reading;
              $readingicon[] = "<IMG SRC=\"../mod/reading/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"Reading\">";
          }
      }
      if (isediting($course->id)) {
          $readingdata[] = "<A HREF=\"mod.php?id=$course->id&section=0&add=reading\">Add reading...</A>";
          $readingicon[] = "&nbsp;";
      }
      print_side_block("", $readingdata, "", $readingicon);

      // Print all the recent activity
      print_simple_box("Recent Activity", $align="CENTER", $width="100%", $color="$THEME->cellheading");
      echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";
      print_recent_activity($course);
      echo "</TD></TR></TABLE>";
      echo "<BR>";

      // Print a form to search forums
      print_simple_box("Search Discussions", $align="CENTER", $width="100%", $color="$THEME->cellheading");
      echo "<DIV ALIGN=CENTER>";
      forum_print_search_form($course);
      echo "</DIV>";

      // Print Admin links for teachers and admin.
      if (isteacher($USER->id) || isadmin()) {
          print_simple_box("Admin", $align="CENTER", $width="100%", $color="$THEME->cellheading");
          $adminicon[]="<IMG SRC=\"../pix/i/edit.gif\" HEIGHT=16 WIDTH=16 ALT=\"Edit\">";
          if (isediting($course->id)) {
              $admindata[]="<A HREF=\"view.php?id=$course->id&edit=off\">Turn editing off</A>";
          } else {
              $admindata[]="<A HREF=\"view.php?id=$course->id&edit=on\">Turn editing on</A>";
          }

          $admindata[]="<A HREF=\"edit.php?id=$course->id\">Course settings...</A>";
          $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"Course\">";
          $admindata[]="<A HREF=\"log.php?id=$course->id\">Logs...</A>";
          $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"Log\">";
          $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">Files...</A>";
          $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"Files\">";

          print_side_block("", $admindata, "", $adminicon);
      }

      echo "</TD>";

      echo "<TD WIDTH=\"55%\" VALIGN=\"TOP\">";
      if ($social = forum_get_course_forum($course->id, "social")) {
          if (forum_is_subscribed($USER->id, $social->id)) {
              $subtext = "Unsubscribe";
          } else {
              $subtext = "Subscribe me by mail";
          }
          $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR><TD>Social Forum - Current Topics<TD ALIGN=RIGHT><FONT SIZE=1><A HREF=\"../mod/forum/subscribe.php?id=$social->id\">$subtext</A></TD></TR></TABLE>";
          print_simple_box("$headertext", $align="CENTER", $width="100%", $color="$THEME->cellheading");
          echo "<IMG ALT=\"\" HEIGHT=7 SRC=\"../pix/spacer.gif\"><BR>";
    
          forum_print_latest_discussions($social->id, 10, "plain", "DESC", false);
          $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

      } else {
          notify("Could not find or create a social forum here");
      }
      ?>
    </TD> 
  </TR> 
</TABLE>

