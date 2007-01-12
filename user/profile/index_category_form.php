<?php //$Id$

require_once("$CFG->dirroot/lib/formslib.php");

class category_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform      =& $this->_form;
        $renderer   =& $mform->defaultRenderer();
        $category   =  $this->_customdata['category'];
        
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id', $category->id);
        $mform->addElement('hidden', 'action', 'editcategory');
        $mform->addElement('hidden', 'sesskey', $USER->sesskey);

        $mform->addElement('text', 'name', get_string('profilecategoryname', 'admin'), 'maxlength="255" size="30"');
        $mform->setType('name', PARAM_MULTILANG);
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setDefault('name', $category->name);
        
        $this->add_action_buttons(true);


    } /// End of function

    function definition_after_data () {
        /// nothing yet
    }


/// perform some moodle validation
    function validation ($data) {
        global $CFG;

        $data  = (object)$data;
        $err = array();

        /// Check the name is unique
        if (($category = get_record('user_info_category', 'name', $data->name)) and ($category->id <> $data->id)) {
            $err['name'] = get_string('profilecategorynamenotunique', 'admin');
        }

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }
}

?>
