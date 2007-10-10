<?php // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managescales', $context);
} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('scales');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'scale', 'courseid'=>$courseid));


$strgrades = get_string('grades');
$pagename  = get_string('scales');

$navigation = grade_build_nav(__FILE__, $pagename, array('courseid' => $courseid));

$strscale          = get_string('scale');
$strstandardscale  = get_string('scalesstandard');
$strcustomscales   = get_string('scalescustom');
$strname           = get_string('name');
$strdelete         = get_string('delete');
$stredit           = get_string('edit');
$srtcreatenewscale = get_string('scalescustomcreate');
$strused           = get_string('used');
$stredit           = get_string('edit');

switch ($action) {
    case 'delete':
        if (!confirm_sesskey()) {
            break;
        }
        $scaleid = required_param('scaleid', PARAM_INT);
        if (!$scale = grade_scale::fetch(array('id'=>$scaleid))) {
            break;
        }

        if (empty($scale->courseid)) {
            require_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM));
        } else if ($scale->courseid != $courseid) {
            error('Incorrect courseid!');
        }

        if (!$scale->can_delete()) {
            break;
        }

        //TODO: add confirmation
        $scale->delete();
        break;
}

if ($courseid) {
    /// Print header
    print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));
    /// Print the plugin selector at the top
    print_grade_plugin_selector($courseid, 'edit', 'scale');

} else {
    admin_externalpage_print_header();
}

if ($courseid and $scales = grade_scale::fetch_all_local($courseid)) {
    print_heading($strcustomscales);
    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $buttons = "";
        $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$scale->id\"><img".
                    " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        if (!$used) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;scaleid=$scale->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table->head  = array($strscale, $strused, $stredit);
    $table->size  = array('70%', '20%', '10%');
    $table->align = array('left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    print_table($table);
}

if ($scales = grade_scale::fetch_all_global()) {
    print_heading($strstandardscale);
    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $buttons = "";
        if (has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
            $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$scale->id\"><img".
                        " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        }
        if (!$used and has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;scaleid=$scale->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table->head  = array($strscale, $strused, $stredit);
    $table->size  = array('70%', '20%', '10%');
    $table->align = array('left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    print_table($table);
}

echo '<div class="buttons">';
print_single_button('edit.php', array('courseid'=>$courseid), $srtcreatenewscale);
echo '</div>';

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}


?>
