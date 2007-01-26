<?php //$Id$

/// Some constants

define ('PROFILE_VISIBLE_ALL',     '2'); // only visible for users with moodle/user:update capability
define ('PROFILE_VISIBLE_PRIVATE', '1'); // either we are viewing our own profile or we have moodle/user:update capability
define ('PROFILE_VISIBLE_NONE',    '0'); // only visible for moodle/user:update capability

/**
 * Base class for the cusomisable profile fields.
 */
class profile_field_base {

    var $field;
    var $inputname;

    /**
     * Constructor method.
     * @param   integer   id of the profile from the user_info_field table
     * @param   integer   id of the user for whom we are displaying data
     */
    function profile_field_base($fieldid) {
        if (!$field = get_record('user_info_field', 'id', $fieldid)) {
            error('Incorrect profile field id!');
        }

        $this->field     = $field;
        $this->inputname = 'profile_field_'.$field->shortname;
    }

    /**
     * Check if the current field is visible to the current user
     * @return  boolean
     */
    function is_visible_for($userid) {
        global $USER;

        switch ($this->field->visible) {
            case PROFILE_VISIBLE_ALL:
                return true;
            case PROFILE_VISIBLE_PRIVATE:
                return ($userid == $USER->id);
            default:
                return has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID));
        }
    }

    /**
     * Print out the form field in the profile page
     * @param   object   instance of the moodleform class
     * $return  boolean
     */
    function display_field(&$form) {

        if ($this->field->visible != PROFILE_VISIBLE_NONE
          or has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {

            $this->display_field_add($form);
            $this->display_field_set_default($form);
            $this->display_field_set_required($form);
            $this->display_field_set_locked($form);
        }
    }

    /**
     * Saves the data coming from form
     * @param   mixed   data coming from the form
     * @return  mixed   returns data id if success of db insert/update, false on fail, 0 if not permitted
     */
    function save_data($usernew) {

        $usernew = $this->save_data_preprocess($usernew);

        if (!isset($usernew->{$this->inputname})) {
            // field not present in form, probably locked and incisible - skip it!
            return;
        }

        $data = new object();
        $data->userid  = $usernew->id;
        $data->fieldid = $this->field->id;
        $data->data    = $usernew->{$this->inputname};

        if ($dataid = get_field('user_info_data', 'id', 'userid', $data->userid, 'fieldid', $data->fieldid)) {
            $data->id = $dataid;
            if (!update_record('user_info_data', $data)) {
                error('Error updating custom profile field!');
            }
        } else {
            insert_record('user_info_data', $data);
        }
    }

    /***** The following methods may be overwritten by child classes *****/

    /**
     * Adds the profile field to the moodle form class
     * @param  form  instance of the moodleform class
     */
    function display_field_add(&$form) {
        error('This abstract method must be overriden');
    }

    /**
     * Validate the form field from profile page
     * @return  string  contains error message otherwise NULL
     **/
    function validate_field($usernew) {
        //no errors by default
        return array();
    }


    function load_data(&$user) {
        if ($data = get_field('user_info_data', 'data', 'userid', $user->id, 'fieldid', $this->field->id)) {
            $user->{$this->inputname} = $data;
        }
    }

    /**
     * Sets the default data for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function display_field_set_default(&$form) {
        if (!empty($default)) {
            $form->setDefault($this->inputname, $this->field->defaultdata);
        }
    }

    /**
     * Sets the required flag for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function display_field_set_required(&$form) {
        if ($this->field->required and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $form->addRule($this->inputname, get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * HardFreeze the field if locked.
     * @param   object   instance of the moodleform class
     */
    function display_field_set_locked(&$form) {
        if ($this->field->locked and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $form->hardFreeze($this->inputname);
        }
    }

    /**
     * Hook for child classess to process the data before it gets saved in database
     * @param   mixed
     * @return  mixed
     */
    function save_data_preprocess($data) {
        return $data;
    }

} /// End of class definition


/***** General purpose functions for customisable user profiles *****/

function profile_load_data(&$user) {
    global $CFG;

    if ($fields = get_records_select('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            $formfield->load_data($user);
        }
    }
}

/**
 * Print out the customisable categories and fields for a users profile
 * @param  object   instance of the moodleform class
 */
function profile_definition(&$form) {
    global $CFG;

    if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        foreach ($categories as $category) {
            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                $form->addElement('header', 'category_'.$category->id, $category->name);
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id);
                    $formfield->display_field($form);

                }
            }
        }
    }
}

function profile_definition_after_data(&$mform) {
    global $CFG;
/*
    if ($fields = get_records('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
//TODO add: method into field class

        }
    }*/
}

function profile_validation($usernew) {
    global $CFG;

    $err = array();
    if ($fields = get_records('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            $err += $formfield->validate_field($usernew);
        }
    }
    return $err;
}

function profile_save_data($usernew) {
    global $CFG;

    if ($fields = get_records_select('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            $formfield->save_data($usernew);
        }
    }
}






?>
