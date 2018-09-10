<?php 
defined('MOODLE_INTERNAL') || die;
class user_image_edit_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('filepicker', 'imagefile', 'Nueva imágen de perfil' , null, array('accepted_types' => 'web_image')); // Add elements to your form
        $mform->addRule('imagefile', null, 'required');
        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
?>