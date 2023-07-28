<?php 
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class manifest_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 

        // add filepicker to upload file..
        $mform->addElement('filepicker', 'manifest_file', get_string('qbmformfile', 'local_qbmanifest'), null,
                   array('maxbytes' => 11111111111111, 'accepted_types' => 'json'));
        $mform->addRule('manifest_file', null, 'required');

        //$this->add_action_buttons();

        $this->add_action_buttons(false, get_string('qbmformsync', 'local_qbmanifest'));
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}