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
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'editcategory');
        $mform->setType('action', PARAM_ACTION);

        $mform->addElement('text', 'name', get_string('profilecategoryname', 'admin'), 'maxlength="255" size="30"');
        $mform->setType('name', PARAM_MULTILANG);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(true);

    } /// End of function

/// perform some moodle validation
    function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $data  = (object)$data;

        $duplicate = record_exists('user_info_category', 'name', $data->name);

        /// Check the name is unique
        if (!empty($data->id)) { // we are editing an existing record
            $olddata = get_record('user_info_category', 'id', $data->id);
            // name has changed, new name in use, new name in use by another record
            $dupfound = (($olddata->name !== $data->name) && $duplicate && ($data->id != $duplicate->id));
        }
        else { // new profile category
            $dupfound = $duplicate;
        }
        
        if ($dupfound ) {
            $errors['name'] = get_string('profilecategorynamenotunique', 'admin');
        }

        return $errors;
    }
}

?>
