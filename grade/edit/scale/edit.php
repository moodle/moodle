<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'edit_form.php';

$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

$systemcontext = get_context_instance(CONTEXT_SYSTEM);

// a bit complex access control :-O
if ($id) {
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
            unset($scale->couseid); // keep previous
        }
        $scale->update();
    }

    redirect($returnurl);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$strscaleedit    = get_string('scale');

if ($courseid) {
    $navigation = grade_build_nav(__FILE__, $strscaleedit, $courseid);
    print_header_simple($strgrades.': '.$strgraderreport, ': '.$strscaleedit, $navigation, '', '', true, '', navmenu($course));

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
    admin_externalpage_print_header();
}

$mform->display();

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}

?>
