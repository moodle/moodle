<?php /* $Id$ */

require_once($CFG->dirroot.'/lib/formslib.php');

class import_outcomes_form extends moodleform {

    function definition() {
        global $COURSE, $USER;

        $mform =& $this->_form;
        //$this->set_upload_manager(new upload_manager('importfile', false, false, null, false, 0, true, true, false));

        $mform->addElement('hidden', 'action', 'upload');
        $mform->addElement('hidden', 'id', $COURSE->id);

        $scope = array();
        if (($COURSE->id > 1) && has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->addElement('radio', 'scope', get_string('importcustom', 'grades'), null, 'custom');
            $mform->addElement('radio', 'scope', get_string('importstandard', 'grades'), null, 'global');
            $mform->setDefault('scope', 'custom');
        }
        
        $mform->addElement('file', 'userfile', get_string('importoutcomes', 'grades'));

        $mform->addElement('submit', 'save', get_string('uploadthisfile'));

    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>
