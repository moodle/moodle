<?php
    // format.php - course format featuring social forum
    //              included from view.php

    require_once("$CFG->dirroot/mod/forum/lib.php");
    require_once("$CFG->dirroot/mod/resource/lib.php");

    // Bounds for block widths
    define('BLOCK_L_MIN_WIDTH', 100);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 100);
    define('BLOCK_R_MAX_WIDTH', 210);

    optional_variable($preferred_width_left, 0);
    optional_variable($preferred_width_right, 0);
    $preferred_width_left = min($preferred_width_left, BLOCK_L_MAX_WIDTH);
    $preferred_width_left = max($preferred_width_left, BLOCK_L_MIN_WIDTH);
    $preferred_width_right = min($preferred_width_right, BLOCK_R_MAX_WIDTH);
    $preferred_width_right = max($preferred_width_right, BLOCK_R_MIN_WIDTH);

    $strgroups       = get_string("groups");
    $strgroupmy      = get_string("groupmy");
    $editing         = isediting($course->id);

    echo '<table width="100%" border="0" cellspacing="5" cellpadding="5">';
    echo '<tr>';

    if(block_have_active($leftblocks) || $editing) {
        echo '<td style="vertical-align: top; width: '.$preferred_width_left.'px;">';
        print_course_blocks($course, $leftblocks, BLOCK_LEFT);
        echo '</td>';
    }

    echo "<td width=\"*\" valign=\"top\">";
    if ($social = forum_get_course_forum($course->id, "social")) {
        if (forum_is_subscribed($USER->id, $social->id)) {
            $subtext = get_string("unsubscribe", "forum");
        } else {
            $subtext = get_string("subscribe", "forum");
        }
        $headertext = "<table border=0 width=100% cellpadding=0 cellspacing=0 class=headingblockcontent><tr><td>".
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
    echo '</td>';

    if(block_have_active($rightblocks) || $editing) {
      echo '<td style="vertical-align: top; width: '.$preferred_width_right.'px;">';
      print_course_blocks($course, $rightblocks, BLOCK_RIGHT);
      if ($editing && !empty($missingblocks)) {
          block_print_blocks_admin($course->id, $missingblocks);
      }
      print_spacer(1, 120, true);
      echo '</td>';
    }

    echo '</tr>';
    echo '</table>';

?>
