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
      print_simple_box(get_string("people"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
      $moddata[]="<A HREF=\"../user/index.php?id=$course->id\">".get_string("listofallpeople")."</A>";
      $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
      $editmyprofile = "<A HREF=\"../user/view.php?id=$USER->id&course=$course->id\">".
                        get_string("editmyprofile")."</A>";
      if ($USER->description) {
          $moddata[]= $editmyprofile;
      } else {
          $moddata[]= $editmyprofile.$blinker;
      }
      $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
      print_side_block("", $moddata, "", $modicon);

      

      // Then, print all the available resources (Section 0)
      print_simple_box(get_string("resources"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
      print_section($site->id, $sections[0], $mods, $modnamesused, true);

      if (isediting($site->id)) {
          echo "<DIV ALIGN=right>";
          popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&section=0&add=",
                      $modnames, "section0", "", get_string("add")."...");
          echo "</DIV>";
      }      

      // Print all the recent activity
      print_simple_box(get_string("recentactivity"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
      echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";
      print_recent_activity($course);
      echo "</TD></TR></TABLE>";
      echo "<BR>";

      // Print a form to search forums
      print_simple_box(get_string("search","forum"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
      echo "<DIV ALIGN=CENTER>";
      forum_print_search_form($course);
      echo "</DIV>";

      // Print Admin links for teachers and admin.
      if (isteacher($course->id)) {
          echo "<BR>";
          $admindata[]="<A HREF=\"edit.php?id=$course->id\">".get_string("settings")."...</A>";
          $adminicon[]="<IMG SRC=\"../pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
          $admindata[]="<A HREF=\"log.php?id=$course->id\">".get_string("logs")."...</A>";
          $adminicon[]="<IMG SRC=\"../pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
          $admindata[]="<A HREF=\"../files/index.php?id=$course->id\">".get_string("files")."...</A>";
          $adminicon[]="<IMG SRC=\"../files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
  
          if ($teacherforum = forum_get_course_forum($course->id, "teacher")) {
              $admindata[]="<A HREF=\"../mod/forum/view.php?f=$teacherforum->id\">".get_string("teacherforum")."</A>";
              $adminicon[]="<IMG SRC=\"../mod/forum/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
          }
  
          $adminicon[]="<IMG SRC=\"../pix/i/edit.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
          if (isediting($course->id)) {
              $admindata[]="<A HREF=\"view.php?id=$course->id&edit=off\">".get_string("turneditingoff")."</A>";
          } else {
              $admindata[]="<A HREF=\"view.php?id=$course->id&edit=on\">".get_string("turneditingon")."</A>";
          }
  
          print_simple_box(get_string("administration"),"CENTER", "100%", $THEME->cellheading);
          print_side_block("", $admindata, "", $adminicon);
      }

      echo "</TD>";

      echo "<TD WIDTH=\"55%\" VALIGN=\"TOP\">";
      if ($social = forum_get_course_forum($course->id, "social")) {
          if (forum_is_subscribed($USER->id, $social->id)) {
              $subtext = get_string("unsubscribe", "forum");
          } else {
              $subtext = get_string("subscribe", "forum");
          }
          $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR><TD>".get_string("socialheadline")."</TD><TD ALIGN=RIGHT><FONT SIZE=1><A HREF=\"../mod/forum/subscribe.php?id=$social->id\">$subtext</A></TD></TR></TABLE>";
          print_simple_box("$headertext", $align="CENTER", $width="100%", $color="$THEME->cellheading");
          echo "<IMG ALT=\"\" HEIGHT=7 SRC=\"../pix/spacer.gif\"><BR>";
    
          forum_print_latest_discussions($social->id, 10, "plain", "DESC", false);
          $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";
          save_session("SESSION");

      } else {
          notify("Could not find or create a social forum here");
      }
      ?>
    </TD> 
  </TR> 
</TABLE>

