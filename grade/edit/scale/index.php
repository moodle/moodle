<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


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

if (!$courseid) {
    admin_externalpage_print_header();
}

$table = null;
$table2 = null;
$heading = '';

if ($courseid and $scales = grade_scale::fetch_all_local($courseid)) {
    $heading = $strcustomscales;

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
}

if ($scales = grade_scale::fetch_all_global()) {
    $heading = $strstandardscale;

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
    $table2->head  = array($strscale, $strused, $stredit);
    $table2->size  = array('70%', '20%', '10%');
    $table2->align = array('left', 'center', 'center');
    $table2->width = '90%';
    $table2->data  = $data;
}


if ($courseid) {
    print_grade_page_head($courseid, 'scale', null, get_string('coursescales', 'grades'));
}

print_heading($strcustomscales, '', 3, 'main');
print_table($table);
print_heading($strstandardscale, '', 3, 'main');
print_table($table2);
echo '<div class="buttons">';
print_single_button('edit.php', array('courseid'=>$courseid), $srtcreatenewscale);
echo '</div>';

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}


?>
