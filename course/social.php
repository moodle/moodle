<?  // $Id$
    // social.php - course format featuring social forum
    //              included from view.php
    
    include_once("$CFG->dirroot/mod/forum/lib.php");
    include_once("$CFG->dirroot/mod/resource/lib.php");

    $leftwidth = 210;
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD WIDTH="<?=$leftwidth?>" VALIGN="TOP"> 
      <? 
      $moddata[]="<A TITLE=\"".get_string("listofallpeople")."\" HREF=\"../user/index.php?id=$course->id\">".get_string("participants")."</A>";
      $modicon[]="<IMG SRC=\"../user/users.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
      $editmyprofile = "<A TITLE=\"$USER->firstname $USER->lastname\" HREF=\"../user/view.php?id=$USER->id&course=$course->id\">".
                        get_string("editmyprofile")."</A>";
      if ($USER->description) {
          $moddata[]= $editmyprofile;
      } else {
          $moddata[]= $editmyprofile." <BLINK>*</BLINK>";
      }
      $modicon[]="<IMG SRC=\"../user/user.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
      print_side_block(get_string("people"), "", $moddata, $modicon, "", $leftwidth);

      

/// Then, print all the available resources (Section 0)
      print_section_block(get_string("activities"), $course, $sections[0], 
                          $mods, $modnames, $modnamesused, true, "100%", isediting($course->id));


/// Print all the recent activity
      // Print all the recent activity
      if ($course->showrecent) {
          print_side_block_start(get_string("recentactivity"), $leftwidth, "sideblockrecentactivity");
          print_recent_activity($course);
          print_side_block_end();
      }


/// Print a form to search forums
      $searchform = forum_print_search_form($course, "", true);
      $searchform = "<DIV ALIGN=\"CENTER\">$searchform</DIV>";
      print_side_block(get_string("search","forum"), $searchform, "", "", "", $leftwidth);

/// Admin links and controls
      print_course_admin_links($course);

      echo "</TD>";

      echo "<TD WIDTH=\"*\" VALIGN=\"TOP\">";
      if ($social = forum_get_course_forum($course->id, "social")) {
          if (forum_is_subscribed($USER->id, $social->id)) {
              $subtext = get_string("unsubscribe", "forum");
          } else {
              $subtext = get_string("subscribe", "forum");
          }
          $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR><TD>".get_string("socialheadline")."</TD><TD ALIGN=RIGHT><FONT SIZE=1><A HREF=\"../mod/forum/subscribe.php?id=$social->id\">$subtext</A></TD></TR></TABLE>";
          print_heading_block($headertext);
          echo "<IMG ALT=\"\" HEIGHT=7 SRC=\"../pix/spacer.gif\"><BR>";
    
          forum_print_latest_discussions($social->id, 10, "plain", "DESC", false);

      } else {
          notify("Could not find or create a social forum here");
      }
      ?>
    </TD> 
  </TR> 
</TABLE>

