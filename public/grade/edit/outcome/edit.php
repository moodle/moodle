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
 * Edit page for grade outcomes.
 *
 * @package   core_grades
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'edit_form.php';

$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/grade/edit/outcome/edit.php');
if ($courseid !== 0) {
    $url->param('courseid', $courseid);
}
if ($id !== 0) {
    $url->param('id', $id);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$systemcontext = context_system::instance();
$heading = get_string('addoutcome', 'grades');

// a bit complex access control :-O
if ($id) {
    $heading = get_string('editoutcome', 'grades');

    /// editing existing outcome
    if (!$outcome_rec = $DB->get_record('grade_outcomes', array('id' => $id))) {
        throw new \moodle_exception('invalidoutcome');
    }
    if ($outcome_rec->courseid) {
        $outcome_rec->standard = 0;
        if (!$course = $DB->get_record('course', array('id' => $outcome_rec->courseid))) {
            throw new \moodle_exception('invalidcourseid');
        }
        require_login($course);
        $context = context_course::instance($course->id);
        require_capability('moodle/grade:manage', $context);
        $courseid = $course->id;
    } else {
        if ($courseid) {
            if (!$course = $DB->get_record('course', array('id' => $courseid))) {
                throw new \moodle_exception('invalidcourseid');
            }
        }
        $outcome_rec->standard = 1;
        $outcome_rec->courseid = $courseid;
        require_login();
        require_capability('moodle/grade:manage', $systemcontext);
        $PAGE->set_context($systemcontext);
    }

} else if ($courseid){
    /// adding new outcome from course
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('moodle/grade:manage', $context);

    $outcome_rec = new stdClass();
    $outcome_rec->standard = 0;
    $outcome_rec->courseid = $courseid;
} else {
    require_login();
    require_capability('moodle/grade:manage', $systemcontext);
    $PAGE->set_context($systemcontext);

    /// adding new outcome from admin section
    $outcome_rec = new stdClass();
    $outcome_rec->standard = 1;
    $outcome_rec->courseid = 0;
}

if (!$courseid) {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');

    $PAGE->set_primary_active_tab('siteadminnode');
} else {
    navigation_node::override_active_url(new moodle_url('/grade/edit/outcome/course.php', ['id' => $courseid]));
    $PAGE->navbar->add(get_string('manageoutcomes', 'grades'),
        new moodle_url('/grade/edit/outcome/index.php', ['id' => $courseid]));
}

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$courseid);
$editoroptions = array(
    'maxfiles'  => EDITOR_UNLIMITED_FILES,
    'maxbytes'  => $CFG->maxbytes,
    'trusttext' => false,
    'noclean'   => true,
    'context'   => $systemcontext
);

if (!empty($outcome_rec->id)) {
    $editoroptions['subdirs'] = file_area_contains_subdirs($systemcontext, 'grade', 'outcome', $outcome_rec->id);
    $outcome_rec = file_prepare_standard_editor($outcome_rec, 'description', $editoroptions, $systemcontext, 'grade', 'outcome', $outcome_rec->id);
} else {
    $editoroptions['subdirs'] = false;
    $outcome_rec = file_prepare_standard_editor($outcome_rec, 'description', $editoroptions, $systemcontext, 'grade', 'outcome', null);
}

$mform = new edit_outcome_form(null, compact('gpr', 'editoroptions'));

$mform->set_data($outcome_rec);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $outcome = new grade_outcome(array('id'=>$id));
    $data->usermodified = $USER->id;

    if (empty($outcome->id)) {
        $data->description = $data->description_editor['text'];
        grade_outcome::set_properties($outcome, $data);
        if (!has_capability('moodle/grade:manage', $systemcontext)) {
            $data->standard = 0;
        }
        $outcome->courseid = !empty($data->standard) ? null : $courseid;
        if (empty($outcome->courseid)) {
            $outcome->courseid = null;
        }
        $outcome->insert();

        $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $systemcontext, 'grade', 'outcome', $outcome->id);
        $DB->set_field($outcome->table, 'description', $data->description, array('id'=>$outcome->id));
    } else {
        $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $systemcontext, 'grade', 'outcome', $id);
        grade_outcome::set_properties($outcome, $data);
        if (isset($data->standard)) {
            $outcome->courseid = !empty($data->standard) ? null : $courseid;
        } else {
            unset($outcome->courseid); // keep previous
        }
        $outcome->update();
    }

    redirect($returnurl);
}

$PAGE->navbar->add($heading, $url);

print_grade_page_head($courseid ?: SITEID, 'outcome', 'edit', $heading, false, false, false);

if (!grade_scale::fetch_all_local($courseid) && !grade_scale::fetch_all_global()) {
    echo $OUTPUT->confirm(get_string('noscales', 'grades'), $CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid, $returnurl);
    echo $OUTPUT->footer();
    die();
}

$mform->display();
echo $OUTPUT->footer();
