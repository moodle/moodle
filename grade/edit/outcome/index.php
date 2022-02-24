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
 * Listing page for grade outcomes.
 *
 * @package   core_grades
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->libdir.'/gradelib.php');

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

$url = new moodle_url('/grade/edit/outcome/index.php', ['id' => $courseid]);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

/// Make sure they can even access this course
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('moodle/grade:manageoutcomes', $context);

    if (empty($CFG->enableoutcomes)) {
        redirect('../../index.php?id='.$courseid);
    }
    // This page doesn't exist on the navigation so map it to another
    navigation_node::override_active_url(new moodle_url('/grade/edit/outcome/course.php', array('id'=>$courseid)));
    $PAGE->navbar->add(get_string('manageoutcomes', 'grades'), $url);
} else {
    if (empty($CFG->enableoutcomes)) {
        redirect('../../../');
    }
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
    $context = context_system::instance();
    $PAGE->set_primary_active_tab('siteadminnode');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'outcome', 'courseid'=>$courseid));

$strshortname        = get_string('outcomeshortname', 'grades');
$strfullname         = get_string('outcomefullname', 'grades');
$strscale            = get_string('scale');
$strstandardoutcome  = get_string('outcomesstandard', 'grades');
$strcustomoutcomes   = get_string('outcomescustom', 'grades');
$strcreatenewoutcome = get_string('outcomecreate', 'grades');
$stritems            = get_string('items', 'grades');
$strcourses          = get_string('courses');
$stredit             = get_string('edit');

switch ($action) {
    case 'delete':
        if (!confirm_sesskey()) {
            break;
        }
        $outcomeid = required_param('outcomeid', PARAM_INT);
        if (!$outcome = grade_outcome::fetch(array('id'=>$outcomeid))) {
            break;
        }

        if (empty($outcome->courseid)) {
            require_capability('moodle/grade:manage', context_system::instance());
        } else if ($outcome->courseid != $courseid) {
            print_error('invalidcourseid');
        }

        if (!$outcome->can_delete()) {
            break;
        }

        $deleteconfirmed = optional_param('deleteconfirmed', 0, PARAM_BOOL);

        if(!$deleteconfirmed){
            $PAGE->set_title(get_string('outcomedelete', 'grades'));
            $PAGE->navbar->add(get_string('outcomedelete', 'grades'));
            echo $OUTPUT->header();
            $confirmurl = new moodle_url('index.php', array(
                    'id' => $courseid, 'outcomeid' => $outcome->id,
                    'action'=> 'delete',
                    'sesskey' =>  sesskey(),
                    'deleteconfirmed'=> 1));

            echo $OUTPUT->confirm(get_string('outcomeconfirmdelete', 'grades', $outcome->fullname), $confirmurl, "index.php?id={$courseid}");
            echo $OUTPUT->footer();
            die;
        }else{
            $outcome->delete();
        }
        break;
}

$systemcontext = context_system::instance();
$caneditsystemscales = has_capability('moodle/course:managescales', $systemcontext);

if ($courseid) {

    $caneditcoursescales = has_capability('moodle/course:managescales', $context);

} else {
    $caneditcoursescales = $caneditsystemscales;
}


$outcomes_tables = array();
$heading = get_string('outcomes', 'grades');

if ($courseid and $outcomes = grade_outcome::fetch_all_local($courseid)) {
    $return = $OUTPUT->heading($strcustomoutcomes, 3, 'main mt-3');
    $data = array();
    foreach($outcomes as $outcome) {
        $line = array();
        $line[] = $outcome->get_name();
        $line[] = $outcome->get_shortname();

        $scale = $outcome->load_scale();
        if (empty($scale->id)) {   // hopefully never happens
            $line[] = $scale->get_name();
            debugging("Found a scale with no ID ({$scale->get_name()}) while outputting course outcomes", DEBUG_DEVELOPER);
        } else {
            if (empty($scale->courseid)) {
                $caneditthisscale = $caneditsystemscales;
            } else if ($scale->courseid == $courseid) {
                $caneditthisscale = $caneditcoursescales;
            } else {
                $context = context_course::instance($scale->courseid);
                $caneditthisscale = has_capability('moodle/course:managescales', $context);
            }
            if ($caneditthisscale) {
                $line[] = grade_print_scale_link($courseid, $scale, $gpr);
            } else {
                $line[] = $scale->get_name();
            }
        }

        $line[] = $outcome->get_item_uses_count();

        $buttons = grade_button('edit', $courseid, $outcome);

        if ($outcome->can_delete()) {
            $buttons .= grade_button('delete', $courseid, $outcome);
        }
        $line[] = $buttons;

        $data[] = $line;
    }
    $table = new html_table();
    $table->head  = array($strfullname, $strshortname, $strscale, $stritems, $stredit);
    $table->size  = array('30%', '20%', '20%', '20%', '10%' );
    $table->align = array('left', 'left', 'left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    $return .= html_writer::table($table);
    $outcomes_tables[] = $return;
}


if ($outcomes = grade_outcome::fetch_all_global()) {
    $return = $OUTPUT->heading($strstandardoutcome, 3, 'main mt-3');
    $data = array();
    foreach($outcomes as $outcome) {
        $line = array();
        $line[] = $outcome->get_name();
        $line[] = $outcome->get_shortname();

        $scale = $outcome->load_scale();
        if (empty($scale->id)) {   // hopefully never happens
            $line[] = $scale->get_name();
            debugging("Found a scale with no ID ({$scale->get_name()}) while outputting global outcomes", DEBUG_DEVELOPER);
        } else {
            if (empty($scale->courseid)) {
                $caneditthisscale = $caneditsystemscales;
            } else if ($scale->courseid == $courseid) {
                $caneditthisscale = $caneditcoursescales;
            } else {
                $context = context_course::instance($scale->courseid);
                $caneditthisscale = has_capability('moodle/course:managescales', $context);
            }
            if ($caneditthisscale) {
                $line[] = grade_print_scale_link($courseid, $scale, $gpr);
            } else {
                $line[] = $scale->get_name();
            }
        }

        $line[] = $outcome->get_course_uses_count();
        $line[] = $outcome->get_item_uses_count();

        $buttons = "";
        if (has_capability('moodle/grade:manage', context_system::instance())) {
            $buttons .= grade_button('edit', $courseid, $outcome);
        }
        if (has_capability('moodle/grade:manage', context_system::instance()) and $outcome->can_delete()) {
            $buttons .= grade_button('delete', $courseid, $outcome);
        }
        $line[] = $buttons;

        $data[] = $line;
    }
    $table = new html_table();
    $table->head  = array($strfullname, $strshortname, $strscale, $strcourses, $stritems, $stredit);
    $table->size  = array('30%', '20%', '20%', '10%', '10%', '10%');
    $table->align = array('left', 'left', 'left', 'center', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    $return .= html_writer::table($table);
    $outcomes_tables[] = $return;
}

$actionbar = new \core_grades\output\manage_outcomes_action_bar($context, !empty($outcomes_tables));
print_grade_page_head($courseid ?: SITEID, 'outcome', 'edit', $heading, false, false,
    true, null, null, null, $actionbar);

// If there are existing outcomes, output the outcome tables.
if (!empty($outcomes_tables)) {
    foreach ($outcomes_tables as $table) {
        echo $table;
    }
} else {
    echo $OUTPUT->notification(get_string('noexistingoutcomes', 'grades'), 'info', false);
}

echo $OUTPUT->footer();

/**
 * Local shortcut function for creating a link to a scale.
 * @param int $courseid The Course ID
 * @param grade_scale $scale The Scale to link to
 * @param grade_plugin_return $gpr An object used to identify the page we just came from
 * @return string html
 */
function grade_print_scale_link($courseid, $scale, $gpr) {
    global $CFG, $OUTPUT;
    $url = new moodle_url('/grade/edit/scale/edit.php', array('courseid' => $courseid, 'id' => $scale->id));
    $url = $gpr->add_url_params($url);
    return html_writer::link($url, $scale->get_name());
}
