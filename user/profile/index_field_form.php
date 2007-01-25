<?php //$Id$

require_once("$CFG->dirroot/lib/formslib.php");

class field_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform     =& $this->_form;
        $fieldtype = $this->_customdata;
        
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'action', 'editfield');
        $mform->addElement('hidden', 'type', $fieldtype);
        $mform->addElement('hidden', 'oldcategory');
        $mform->addElement('hidden', 'datatype');
        $mform->addElement('hidden', 'sesskey');
        

        /// Everything else is dependant on the data type
        require_once($CFG->dirroot.'/user/profile/field/'.$fieldtype.'/field.class.php');
        $newfield = 'profile_field_'.$fieldtype;
        $formfield = new $newfield();
        $formfield->edit_field($mform);
        

        $this->add_action_buttons(true);


    } /// End of function

    function definition_after_data () {
        /// nothing yet
    }


/// perform some moodle validation
    function validation ($data) {
        global $CFG;

        $data  = (object)$data;
        $fieldtype = $this->_customdata;
        
        /// Everything else is dependant on the data type
        require_once($CFG->dirroot.'/user/profile/field/'.$fieldtype.'/field.class.php');
        $newfield = 'profile_field_'.$fieldtype;
        $formfield = new $newfield();
        $err = $formfield->edit_validate($data);

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }
}

?>
