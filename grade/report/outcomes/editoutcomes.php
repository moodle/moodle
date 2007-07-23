<?php

include_once('../../../config.php');
require_once $CFG->libdir.'/formslib.php';
class edit_outcomes_form extends moodleform {

    function definition() {
        global $CFG, $COURSE;

        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('outcomes'));

        $mform->addElement('text', 'shortname', get_string('shortname'));
        $mform->addRule('shortname', get_string('required'), 'required');
        $mform->setType('id', PARAM_TEXT);

        $mform->addElement('text', 'fullname', get_string('fullname'));
        $mform->addRule('fullname', get_string('required'), 'required');
        $mform->setType('id', PARAM_TEXT);

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
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}

$id = optional_param('id', 0, PARAM_INT); // id of the outcome
if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    // optional course id, if set, editting from course
} else {
    // admin editting site level outcomes
    $returnurl = $CFG->wwwroot."/grade/report/outcomes/settings.php";
}
// form processing

$mform = new edit_outcomes_form();
if ($id) {
    // form set data
    $mform->set_data(get_record('grade_outcomes', 'id', $id));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($data = $mform->get_data()) {
    if ($data->id) {
        update_record('grade_outcomes', $data);
    } else {
        insert_record('grade_outcomes', $data);
    }
    redirect($returnurl);
}

// Add tabs
$currenttab = 'editoutcomes';
include('tabs.php');

print_header();
$mform->display();
print_footer();
?>
