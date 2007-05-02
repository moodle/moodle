<?php
require_once $CFG->libdir.'/formslib.php';

class admin_uploaduser_form extends moodleform {
    function definition (){
        $mform =& $this->_form;

        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $mform->addElement('header', 'settingsheader', get_string('settings'));

        $passwordopts = array( 0 => get_string('infilefield', 'auth'),
                               1 => get_string('createpasswordifneeded', 'auth'),
                              );

        $mform->addElement('select', 'createpassword', get_string('passwordhandling', 'auth'), $passwordopts);

        $mform->addElement('selectyesno', 'updateaccounts', get_string('updateaccounts', 'admin'));
        $mform->addElement('selectyesno', 'allowrenames', get_string('allowrenames', 'admin'));

        $this->add_action_buttons(false, get_string('uploadusers'));
    }

    function get_userfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
	  // return the temporary filename to process
            return $this->_upload_manager->files['userfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }
}
?>
