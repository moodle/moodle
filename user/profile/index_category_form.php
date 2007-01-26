<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class category_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform =& $this->_form;

        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'action', 'editcategory');

        $mform->addElement('text', 'name', get_string('profilecategoryname', 'admin'), 'maxlength="255" size="30"');
        $mform->setType('name', PARAM_MULTILANG);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(true);

    } /// End of function

/// perform some moodle validation
    function validation ($data) {
        global $CFG;

        $data  = (object)$data;
        $err = array();

        $category = get_record('user_info_category', 'id', $data->id);

        /// Check the name is unique
        if ($category and ($category->name !== $data->name) and (record_exists('user_info_category', 'name', $data->name))) {
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
