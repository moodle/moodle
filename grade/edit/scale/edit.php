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
$heading = '';

// a bit complex access control :-O
if ($id) {
    $heading = get_string('editscale', 'grades');

    /// editing existing scale
    if (!$scale_rec = get_record('scale', 'id', $id)) {
        error('Incorrect scale id');
    }
    if ($scale_rec->courseid) {
        $scale_rec->standard = 0;
        if (!$course = get_record('course', 'id', $scale_rec->courseid)) {
            error('Incorrect course id');
        }
        require_login($course);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:managescales', $context);
        $courseid = $course->id;
    } else {
        if ($courseid) {
            if (!$course = get_record('course', 'id', $courseid)) {
                error('Incorrect course id');
            }
        }
        $scale_rec->standard = 1;
        $scale_rec->courseid = $courseid;
        require_login($courseid);
        require_capability('moodle/course:managescales', $systemcontext);
    }

} else if ($courseid){
    $heading = get_string('addscale', 'grades');
    /// adding new scale from course
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    $scale_rec = new object();
    $scale_rec->standard = 0;
    $scale_rec->courseid = $courseid;
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managescales', $context);

} else {
    /// adding new scale from admin section
    $scale_rec = new object();
    $scale_rec->standard = 1;
    $scale_rec->courseid = 0;
    require_login();
    require_capability('moodle/course:managescales', $systemcontext);
}

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$courseid);

$mform = new edit_scale_form(null, array('gpr'=>$gpr));

$mform->set_data($scale_rec);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data(false)) {
    $scale = new grade_scale(array('id'=>$id));
    $data->userid = $USER->id;
    grade_scale::set_properties($scale, $data);

    if (empty($scale->id)) {
        if (!has_capability('moodle/grade:manage', $systemcontext)) {
            $data->standard = 0;
        }
        $scale->courseid = !empty($data->standard) ? 0 : $courseid;
        $scale->insert();

    } else {
        if (isset($data->standard)) {
            $scale->courseid = !empty($data->standard) ? 0 : $courseid;
        } else {
            unset($scale->courseid); // keep previous
        }
        $scale->update();
    }
    redirect($returnurl);
}

if ($courseid) {
    print_grade_page_head($course->id, 'scale', 'edit', $heading);

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('scales');
    admin_externalpage_print_header();
}

$mform->display();

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}

?>
