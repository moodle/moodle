<?php // $Id$
      // format.php - course format featuring social forum
      //              included from view.php

    if (!empty($THEME->customcorners)) {
        require_once($CFG->dirroot.'/lib/custom_corners_lib.php');
    }
    
    // Bounds for block widths
    // more flexible for theme designers taken from theme config.php
    $lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
    $lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
    $rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
    $rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

    define('BLOCK_L_MIN_WIDTH', $lmin);
    define('BLOCK_L_MAX_WIDTH', $lmax);
    define('BLOCK_R_MIN_WIDTH', $rmin);
    define('BLOCK_R_MAX_WIDTH', $rmax);
  
    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                            BLOCK_R_MAX_WIDTH);

    $strgroups  = get_string('groups');
    $strgroupmy = get_string('groupmy');
    $editing    = $PAGE->user_is_editing();

    echo '<table id="layout-table" cellspacing="0" summary="'.get_string('layouttable').'">';
    echo '<tr>';

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width:'.$preferred_width_left.'px" id="left-column">';
        if (!empty($THEME->customcorners)) print_custom_corners_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        if (!empty($THEME->customcorners)) print_custom_corners_end();
        echo '</td>';
    }

    echo '<td id="middle-column">';
    if (!empty($THEME->customcorners)) print_custom_corners_start();
    echo '<a name="startofcontent"></a>';
    if ($forum = forum_get_course_forum($course->id, 'social')) {
        print_heading_block(get_string('socialheadline'));
        if (forum_is_forcesubscribed($forum->id)) {
            echo '<div class="subscribelink">'.get_string('everyoneissubscribed', 'forum').'</div>';
        } else if (forum_is_subscribed($USER->id, $forum->id)) {
            echo '<div class="subscribelink"><a href="../mod/forum/subscribe.php?id='.$forum->id.'">'.get_string('unsubscribe', 'forum').'</a></div>';
        } else {
            echo '<div class="subscribelink"><a href="../mod/forum/subscribe.php?id='.$forum->id.'">'.get_string('subscribe', 'forum').'</a></div>';
        }

        forum_print_latest_discussions($course, $forum, 10, 'plain', '', false);

    } else {
        notify('Could not find or create a social forum here');
    }
    if (!empty($THEME->customcorners)) print_custom_corners_end();
    echo '</td>';

    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
        echo '<td style="width:'.$preferred_width_right.'px" id="right-column">';
        if (!empty($THEME->customcorners)) print_custom_corners_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        if (!empty($THEME->customcorners)) print_custom_corners_end();
        echo '</td>';
    }

    echo '</tr>';
    echo '</table>';

?>
