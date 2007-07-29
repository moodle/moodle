<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'edit_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context       = get_context_instance(CONTEXT_COURSE, $course->id);
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/course:managescales', $context);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$course->id);

$mform = new edit_scale_form(null, array('gpr'=>$gpr));
if ($scale = get_record('scale', 'id', $id)) {
    $scale->custom = (int)(!empty($scale->courseid));
    if (!empty($scale->courseid)) {
        require_capability('moodle/course:managescales', $systemcontext);
    }
    $scale->courseid = $courseid;
    $options = new object();
    $options->smiley  = false;
    $options->filter  = false;
    $options->noclean = false;
    $scale->description = format_text($scale->description, FORMAT_MOODLE, $options);
    $mform->set_data($scale);

} else {
    $mform->set_data(array('courseid'=>$courseid, 'custom'=>1));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data(false)) {
    $scale = new grade_scale(array('id'=>$id));
    $data->userid = $USER->id;
    grade_scale::set_properties($scale, $data);

    if (empty($scale->id)) {
        $scale->courseid = $courseid;
        if (empty($data->custom) and has_capability('moodle/course:managescales', $systemcontext)) {
            $scale->courseid = 0;
        }
        $scale->insert();

    } else {
        if (isset($data->custom)) {
            $scale->courseid = $data->custom ? $courseid : 0;
        } else {
            unset($scale->couseid);
        }
        $scale->update();
    }

    redirect($returnurl, 'temp debug delay', 3);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$strscaleedit    = get_string('scale');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strscaleedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strscaleedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);
die;
