<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class course_import_groups_form extends moodleform {

    function definition() {
        global $CFG, $USER;
        $mform =& $this->_form;
        $strimportgroups = get_string('importgroups');
        $maxsize = get_max_upload_file_size();

        //fill in the data depending on page params
        //later using set_data
        $mform->addElement('header', 'general');

        $filepickeroptions = array();
        $filepickeroptions['filetypes'] = '*';
        $filepickeroptions['maxbytes'] = $maxsize;
        $mform->addElement('filepicker', 'userfile', get_string('import'), null, $filepickeroptions);

        $this->add_action_buttons(false, $strimportgroups);
    }
}

