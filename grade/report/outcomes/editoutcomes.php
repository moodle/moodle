<?php

include_once('../../../config.php');
require_once $CFG->libdir.'/formslib.php';

// courseid needs to be passed in to know whether this should be tied to a course
class edit_outcomes_form extends moodleform {

    function definition() {
        global $CFG, $COURSE;

        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('outcomes'));

        $mform->addElement('text', 'shortname', get_string('shortname'));
        $mform->addRule('shortname', get_string('required'), 'required');
        $mform->setType('shortname', PARAM_TEXT);

        $mform->addElement('text', 'fullname', get_string('fullname'));
        $mform->addRule('fullname', get_string('required'), 'required');
        $mform->setType('fullname', PARAM_TEXT);

        $scalearr = array();
        if ($scales = get_records('scale')) {
            foreach ($scales as $scale) {
                $scalearr[$scale->id] = $scale->name;
            }
        }

        $mform->addElement('select', 'scaleid', get_string('scale'), $scalearr);
        $mform->addRule('scaleid', get_string('required'), 'required');
        $mform->setType('scaleid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}

$id = optional_param('id', 0, PARAM_INT); // id of the outcome
if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    // optional course id, if set, editting from course
    require_login($courseid);
    require_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_COURSE, $courseid));
    $returnurl = $CFG->wwwroot."/grade/report/outcomes/course.php?id=$courseid";
} else {
    // admin editting site level outcomes
    require_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_SYSTEM));
    $returnurl = $CFG->wwwroot."/grade/report/outcomes/site.php";
}
// form processing

$mform = new edit_outcomes_form();
if ($id) {
    // form set data
    $mform->set_data(get_record('grade_outcomes', 'id', $id));
}
// if courseid is provided, set it in the form
if ($courseid) {
    $data->courseid = $courseid;
    $mform->set_data($data);
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($data = $mform->get_data()) {
    if ($data->courseid == 0) {
        $data->courseid = NULL;
    }

    if ($data->id) {
        update_record('grade_outcomes', $data);
    } else {
        insert_record('grade_outcomes', $data);
    }
    redirect($returnurl);
}

// Build navigation
$strgrades = get_string('grades');
$stroutcomes = get_string('outcomes', 'grades');
$navlinks = array();
$navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
$navlinks[] = array('name' => $stroutcomes, 'link' => '', 'type' => 'misc');

$navigation = build_navigation($navlinks);
/// Print header
print_header_simple($strgrades.':'.$stroutcomes, ':'.$strgrades, $navigation, '', '', true);
// Add tabs
$currenttab = 'editoutcomes';
include('tabs.php');
$mform->display();
print_footer();
?>