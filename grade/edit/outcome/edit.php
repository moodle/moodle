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
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'edit_form.php';

$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$heading = null;

// a bit complex access control :-O
if ($id) {
    $heading = get_string('editoutcome', 'grades');

    /// editing existing outcome
    if (!$outcome_rec = get_record('grade_outcomes', 'id', $id)) {
        error('Incorrect outcome id');
    }
    if ($outcome_rec->courseid) {
        $outcome_rec->standard = 0;
        if (!$course = get_record('course', 'id', $outcome_rec->courseid)) {
            error('Incorrect course id');
        }
        require_login($course);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/grade:manage', $context);
        $courseid = $course->id;
    } else {
        if ($courseid) {
            if (!$course = get_record('course', 'id', $courseid)) {
                error('Incorrect course id');
            }
        }
        $outcome_rec->standard = 1;
        $outcome_rec->courseid = $courseid;
        require_login();
        require_capability('moodle/grade:manage', $systemcontext);
    }

} else if ($courseid){
    $heading = get_string('addoutcome', 'grades');
    /// adding new outcome from course
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    $outcome_rec = new object();
    $outcome_rec->standard = 0;
    $outcome_rec->courseid = $courseid;
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/grade:manage', $context);

} else {
    /// adding new outcome from admin section
    $outcome_rec = new object();
    $outcome_rec->standard = 1;
    $outcome_rec->courseid = 0;
    require_login();
    require_capability('moodle/grade:manage', $systemcontext);
}

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$courseid);

$mform = new edit_outcome_form(null, array('gpr'=>$gpr));

$mform->set_data($outcome_rec);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data(false)) {
    $outcome = new grade_outcome(array('id'=>$id));
    $data->usermodified = $USER->id;
    grade_outcome::set_properties($outcome, $data);

    if (empty($outcome->id)) {
        if (!has_capability('moodle/grade:manage', $systemcontext)) {
            $data->standard = 0;
        }
        $outcome->courseid = !empty($data->standard) ? null : $courseid;
        if (empty($outcome->courseid)) {
            $outcome->courseid = null;
        }
        $outcome->insert();

    } else {
        if (isset($data->standard)) {
            $outcome->courseid = !empty($data->standard) ? null : $courseid;
        } else {
            unset($outcome->courseid); // keep previous
        }
        $outcome->update();
    }

    redirect($returnurl);
}

if ($courseid) {
    print_grade_page_head($courseid, 'outcome', 'edit', $heading);
} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
    admin_externalpage_print_header();
}

if (!grade_scale::fetch_all_local($courseid) && !grade_scale::fetch_all_global()) {
    notice_yesno(get_string('noscales', 'grades'), $CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid, $returnurl);
    print_footer($course);
    die();
}

$mform->display();

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}

?>
