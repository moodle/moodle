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

/**
 * A page for managing custom and standard scales
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url('/grade/edit/scale/index.php', array('id' => $courseid));

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        print_error('invalidcourseid');
    }
    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('moodle/course:managescales', $context);
    $PAGE->set_pagelayout('admin');
} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('scales');
    $context = context_system::instance();
    $PAGE->set_primary_active_tab('siteadminnode');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'scale', 'courseid'=>$courseid));

$strscale          = get_string('scale');
$strstandardscale  = get_string('scalesstandard');
$strcustomscales   = get_string('scalescustom');
$strname           = get_string('name');
$strdelete         = get_string('delete');
$stredit           = get_string('edit');
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
            require_capability('moodle/course:managescales', context_system::instance());
        } else if ($scale->courseid != $courseid) {
            print_error('invalidcourseid');
        }

        if (!$scale->can_delete()) {
            break;
        }

        $deleteconfirmed = optional_param('deleteconfirmed', 0, PARAM_BOOL);

        if (!$deleteconfirmed) {
            if ($courseid) {
                $PAGE->navbar->add(get_string('scales'), new moodle_url('/grade/edit/scale/index.php',
                    ['id' => $courseid]));
            }
            $strdeletescale = get_string('deletescale', 'grades');
            $PAGE->navbar->add($strdeletescale);
            $PAGE->set_title($strdeletescale);
            $PAGE->set_heading($COURSE->fullname);
            echo $OUTPUT->header();
            $confirmurl = new moodle_url('index.php', array(
                    'id' => $courseid, 'scaleid' => $scale->id,
                    'action'=> 'delete',
                    'sesskey' =>  sesskey(),
                    'deleteconfirmed'=> 1));

            echo $OUTPUT->confirm(get_string('scaleconfirmdelete', 'grades', $scale->get_name()), $confirmurl,
                "index.php?id={$courseid}");
            echo $OUTPUT->footer();
            die;
        } else {
            $scale->delete();
        }
        break;
}

if (!$courseid) {
    echo $OUTPUT->header();
}

$table = new html_table();
$table2 = new html_table();
$heading = '';

if ($courseid and $scales = grade_scale::fetch_all_local($courseid)) {
    $heading = $strcustomscales;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = $scale->get_name() .'<div class="scale_options">'.str_replace(",", ", ", $scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $buttons = "";
        $buttons .= grade_button('edit', $courseid, $scale);
        if (!$used) {
            $buttons .= grade_button('delete', $courseid, $scale);
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table->head  = array($strscale, $strused, $stredit);
    $table->size  = array('70%', '20%', '10%');
    $table->align = array('left', 'center', 'center');
    $table->attributes['class'] = 'scaletable localscales generaltable';
    $table->data  = $data;
}

if ($scales = grade_scale::fetch_all_global()) {
    $heading = $strstandardscale;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = $scale->get_name().'<div class="scale_options">'.str_replace(",", ", ", $scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $buttons = "";
        if (has_capability('moodle/course:managescales', context_system::instance())) {
            $buttons .= grade_button('edit', $courseid, $scale);
        }
        if (!$used and has_capability('moodle/course:managescales', context_system::instance())) {
            $buttons .= grade_button('delete', $courseid, $scale);
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table2->head  = array($strscale, $strused, $stredit);
    $table->attributes['class'] = 'scaletable globalscales generaltable';
    $table2->size  = array('70%', '20%', '10%');
    $table2->align = array('left', 'center', 'center');
    $table2->data  = $data;
}

$actionbar = new \core_grades\output\scales_action_bar($context);

if ($courseid) {
    print_grade_page_head($courseid, 'scale', 'scale', get_string('coursescales', 'grades'),
        false, false, true, null, null, null, $actionbar);
} else {
    $renderer = $PAGE->get_renderer('core_grades');
    echo $renderer->render_action_bar($actionbar);
    echo $OUTPUT->heading(get_string('scales', 'core'));
}

$hascustomscales = !empty($table->data);
$hasstandardscales = !empty($table2->data);

// If there are custom scales available in this context, output the custom scales table and a heading.
if ($hascustomscales) {
    echo $OUTPUT->heading($strcustomscales, 3, 'main mt-3');
    echo html_writer::table($table);
}
// If there are standard scales available in this context, output the standard scales table and a heading.
if ($hasstandardscales) {
    echo $OUTPUT->heading($strstandardscale, 3, 'main  mt-3');
    echo html_writer::table($table2);
}
// If the are no available scales, display a notification.
if (!$hascustomscales && !$hasstandardscales) {
    echo $OUTPUT->notification(get_string('noexistingscales', 'grades'), 'info', false);
}
echo $OUTPUT->footer();
