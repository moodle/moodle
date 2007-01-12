<?php //$Id$

require_once("$CFG->dirroot/lib/formslib.php");

class field_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform      =& $this->_form;
        $renderer   =& $mform->defaultRenderer();
        $field       = $this->_customdata['field'];
        
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id', $field->id);
        $mform->addElement('hidden', 'action', 'editfield');
        $mform->addElement('hidden', 'type', $field->datatype);
        $mform->addElement('hidden', 'oldcategory', $field->categoryid);
        $mform->addElement('hidden', 'datatype', $field->datatype);
        $mform->addElement('hidden', 'sesskey', $USER->sesskey);
        

        /// Everything else is dependant on the data type
        require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
        $newfield = 'profile_field_'.$field->datatype;
        $formfield = new $newfield($field->id);
        $formfield->edit_field($mform);
        
        /// override the defaults with the user settings
        $this->set_defaults($field);

        $this->add_action_buttons(true);


    } /// End of function

    function definition_after_data () {
        /// nothing yet
    }


/// perform some moodle validation
    function validation ($data) {
        global $CFG;

        $data  = (object)$data;
        $field = $this->_customdata['field'];
        
        /// Everything else is dependant on the data type
        require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
        $newfield = 'profile_field_'.$field->datatype;
        $formfield = new $newfield($field->id);
        $err = $formfield->edit_validate($data);

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }
}

?>
