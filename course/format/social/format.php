<?php  // $Id$
    // social.php - course format featuring social forum
    //              included from view.php
    
    require_once("$CFG->dirroot/mod/forum/lib.php");
    require_once("$CFG->dirroot/mod/resource/lib.php");

    $leftwidth = 210;
    $strgroups       = get_string("groups");
    $strgroupmy      = get_string("groupmy");
?>

<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="<?php echo $leftwidth?>" valign="top"> 
      <?php 
      $moddata[]="<a title=\"".get_string("listofallpeople")."\" href=\"../user/index.php?id=$course->id\">".get_string("participants")."</a>";
      $modicon[]="<img src=\"$CFG->pixpath/i/users.gif\" height=16 width=16 alt=\"\">";

      if ($course->groupmode or !$course->groupmodeforce) {
          if ($course->groupmode == VISIBLEGROUPS or isteacheredit($course->id)) {
              $moddata[]="<a title=\"$strgroups\" href=\"groups.php?id=$course->id\">$strgroups</a>";
              $modicon[]="<img src=\"$CFG->pixpath/i/group.gif\" height=16 width=16 alt=\"\">";
          } else if ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce) {
              // Show nothing
          } else if ($currentgroup = get_current_group($course->id)) {
              $moddata[]="<a title=\"$strgroupmy\" href=\"group.php?id=$course->id\">$strgroupmy</a>";
              $modicon[]="<img src=\"$CFG->pixpath/i/group.gif\" height=16 width=16 alt=\"\">";
          }
      }

      $fullname = fullname($USER, true);
      $editmyprofile = "<a title=\"$fullname\" href=\"../user/edit.php?id=$USER->id&course=$course->id\">".
                        get_string("editmyprofile")."</A>";
      if ($USER->description) {
          $moddata[]= $editmyprofile;
      } else {
          $moddata[]= $editmyprofile." <blink>*</blink>";
      }
      $modicon[]="<img src=\"$CFG->pixpath/i/user.gif\" height=16 width=16 alt=\"\">";
      print_side_block(get_string("people"), "", $moddata, $modicon, "", $leftwidth);

      
/// Print a form to search forums
      $searchform = forum_print_search_form($course, "", true);
      $searchform = "<div align=\"center\">$searchform</div>";
      print_side_block(get_string("search","forum"), $searchform, "", "", "", $leftwidth);


/// Then, print all the available resources (Section 0)
      print_section_block(get_string("activities"), $course, $sections[0], 
                          $mods, $modnames, $modnamesused, true, "100%");


/// Print all the recent activity
      // Print all the recent activity
      if ($course->showrecent) {
          print_side_block_start(get_string("recentactivity"), $leftwidth, "sideblockrecentactivity");
          print_recent_activity($course);
          print_side_block_end();
      }


/// Admin links and controls
      print_course_admin_links($course);

/// My courses
      print_courses_sideblock(0, "$leftwidth");

      echo "</td>";

      echo "<td width=\"*\" valign=\"top\">";
      if ($social = forum_get_course_forum($course->id, "social")) {
          if (forum_is_subscribed($USER->id, $social->id)) {
              $subtext = get_string("unsubscribe", "forum");
          } else {
              $subtext = get_string("subscribe", "forum");
          }
          $headertext = "<table border=0 width=100% cellpadding=0 cellspacing=0><tr><td>".
                         get_string("socialheadline").
                         "</td><td align=right><font size=1>".
                         "<a href=\"../mod/forum/subscribe.php?id=$social->id\">$subtext</a></td>".
                         "</tr></table>";
          print_heading_block($headertext);
          echo "<img alt=\"\" height=7 src=\"../pix/spacer.gif\"><br>";
    
          forum_print_latest_discussions($social->id, 10, "plain", "", false);

      } else {
          notify("Could not find or create a social forum here");
      }
      ?>
    </td> 
  </tr> 
</table>

