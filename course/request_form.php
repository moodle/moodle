<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class course_request_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        $mform->addElement('text', 'fullname', get_string('fullname'), 'maxlength="254" size="50"');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement('text', 'shortname', get_string('shortname'), 'maxlength="100" size="10"');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        $mform->addElement('htmleditor', 'summary', get_string('summary'), array('rows'=>'15', 'cols'=>'50'));
        $mform->addRule('summary', get_string('missingsummary'), 'required', null, 'client');
        $mform->setType('summary', PARAM_RAW);
        $mform->setHelpButton('summary', array('text', get_string('helptext')));


        $mform->addElement('textarea', 'reason', get_string('courserequestreason'), array('rows'=>'15', 'cols'=>'50'));
        $mform->addRule('reason', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('reason', PARAM_TEXT);

        $mform->addElement('text', 'password', get_string('enrolmentkey'), 'size="25"');
        $mform->setType('password', PARAM_RAW);


        $this->add_action_buttons();
    }

    function validation($data) {
        $errors = array();
        $foundcourses = null;
        $foundreqcourses = null;

        if (!empty($data['shortname'])) {
            $foundcourses = get_records('course', 'shortname', $data['shortname']);
            $foundreqcourses = get_records('course_request', 'shortname', $data['shortname']);
        }
        if (!empty($foundreqcourses)) {
            if (!empty($foundcourses)) {
                $foundcourses = array_merge($foundcourses, $foundreqcourses);
            } else {
                $foundcourses = $foundreqcourses;
            }
        }

        if (!empty($foundcourses)) {

            if (!empty($foundcourses)) {
                foreach ($foundcourses as $foundcourse) {
                    if (isset($foundcourse->requester) && $foundcourse->requester) {
                        $pending = 1;
                        $foundcoursenames[] = $foundcourse->fullname.' [*]';
                    } else {
                        $foundcoursenames[] = $foundcourse->fullname;
                    }
                }
                $foundcoursenamestring = addslashes(implode(',', $foundcoursenames));

                $errors['shortname'] = get_string('shortnametaken', '', $foundcoursenamestring);
                if (!empty($pending)) {
                    $errors['shortname'] .= get_string('starpending');
                }
            }
        }
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }

}
?>
