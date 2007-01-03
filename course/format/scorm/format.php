<?php // $Id$
      // format.php - course format featuring single activity
      //              included from view.php

    require_once("$CFG->dirroot/mod/forum/lib.php");
    $module = $course->format;
    require_once($CFG->dirroot.'/mod/'.$module.'/locallib.php');

    // Bounds for block widths
    define('BLOCK_L_MIN_WIDTH', 100);
    define('BLOCK_L_MAX_WIDTH', 210);
    define('BLOCK_R_MIN_WIDTH', 100);
    define('BLOCK_R_MAX_WIDTH', 210);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                            BLOCK_R_MAX_WIDTH);

    $strgroups  = get_string('groups');
    $strgroupmy = get_string('groupmy');
    $editing    = $PAGE->user_is_editing();

    echo '<table id="layout-table" cellspacing="0">';
    echo '<tr>';

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td width="'.$preferred_width_left.'" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column"><a name="startofcontent"></a>';
    $moduleformat = $module.'_course_format_display';
    if (function_exists($moduleformat)) {
        $moduleformat($USER,$course);
    } else { 
        notify('The module '. $module. ' does not support single activity course format');
    }
    echo '</td>';

    // The right column
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
        echo '<td width="'.$preferred_width_right.'" id="right-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</td>';
    }

    echo '</tr>';
    echo '</table>';

?>
