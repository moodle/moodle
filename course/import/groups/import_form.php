<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class course_import_groups_form extends moodleform {

    function definition() {

        global $CFG, $USER;
        $mform    =& $this->_form;
        $maxuploadsize = $this->_customdata['maxuploadsize'];
        $strimportgroups = get_string("importgroups");

        $this->set_upload_manager(new upload_manager('userfile', true, false, '', false, $maxuploadsize, true, true));
        //$this->set_max_file_size('', $maxuploadsize);

        $mform->addElement('header', 'general', '');//fill in the data depending on page params
        //later using set_data
        // buttons

        $mform->addElement('file', 'userfile', '');
        $mform->setHelpButton('userfile', array('attachment', get_string('attachment', 'forum'), 'forum'));


        $this->add_action_buttons(false, $strimportgroups);

    }
    function get_import_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['userfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }
}
?>
