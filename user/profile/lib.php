<?php //$Id$

/// Some constants

define ('PROFILE_VISIBLE_ALL',     '2'); // only visible for users with moodle/user:update capability
define ('PROFILE_VISIBLE_PRIVATE', '1'); // either we are viewing our own profile or we have moodle/user:update capability
define ('PROFILE_VISIBLE_NONE',    '0'); // only visible for moodle/user:update capability



/**
 * Base class for the cusomisable profile fields.
 */
class profile_field_base {

    /// These 2 variables are really what we're interested in.
    /// Everything else can be extracted from them
    var $fieldid;
    var $userid;
    
    var $field;
    var $inputname;
    var $data;

    /**
     * Constructor method.
     * @param   integer   id of the profile from the user_info_field table
     * @param   integer   id of the user for whom we are displaying data
     */
    function profile_field_base($fieldid=0, $userid=0) {
        global $USER;

        $this->set_fieldid($fieldid);
        $this->set_userid($userid);
        $this->load_data();
    }


/***** The following methods must be overwritten by child classes *****/

    /**
     * Abstract method: Adds the profile field to the moodle form class
     * @param  form  instance of the moodleform class
     */
    function edit_field_add(&$mform) {
        error('This abstract method must be overriden');
    }

    
/***** The following methods may be overwritten by child classes *****/

    /**
     * Display the data for this field
     */
    function display_data() {
        return s(format_string($this->data));
    }
    
    /**
     * Print out the form field in the edit profile page
     * @param   object   instance of the moodleform class
     * $return  boolean
     */
    function edit_field(&$mform) {

        if ($this->field->visible != PROFILE_VISIBLE_NONE
          or has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {

            $this->edit_field_add($mform);
            $this->edit_field_set_default($mform);
            $this->edit_field_set_required($mform);
            $this->edit_field_set_locked($mform);
        }
    }

    /**
     * Saves the data coming from form
     * @param   mixed   data coming from the form
     * @return  mixed   returns data id if success of db insert/update, false on fail, 0 if not permitted
     */
    function edit_save_data($usernew) {

        if (!isset($usernew->{$this->inputname})) {
            // field not present in form, probably locked and invisible - skip it
            return;
        }
        
        $usernew->{$this->inputname} = $this->edit_save_data_preprocess($usernew->{$this->inputname});

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

    /**
     * Validate the form field from profile page
     * @return  string  contains error message otherwise NULL
     **/
    function edit_validate_field($usernew) {
        //no errors by default
        return array();
    }

    /**
     * Sets the default data for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function edit_field_set_default(&$mform) {
        if (!empty($default)) {
            $mform->setDefault($this->inputname, $this->field->defaultdata);
        }
    }

    /**
     * Sets the required flag for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function edit_field_set_required(&$mform) {
        if ($this->is_required() and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $mform->addRule($this->inputname, get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * HardFreeze the field if locked.
     * @param   object   instance of the moodleform class
     */
    function edit_field_set_locked(&$mform) {
        if ($this->is_locked() and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $mform->hardFreeze($this->inputname);
        }
    }

    /**
     * Hook for child classess to process the data before it gets saved in database
     * @param   mixed
     * @return  mixed
     */
    function edit_save_data_preprocess($data) {
        return $data;
    }

    /**
     * Loads a user object with data for this field ready for the edit profile
     * form
     * @param   object   a user object
     */
    function edit_load_user_data(&$user) {
        if ($this->data !== NULL) {
            $user->{$this->inputname} = $this->data;
        }
    }


/***** The following methods generally should not be overwritten by child classes *****/
   
    /**
     * Accessor method: set the userid for this instance
     * @param   integer   id from the user table
     */
    function set_userid($userid) {
        $this->userid = $userid;
    }

    /**
     * Accessor method: set the fieldid for this instance
     * @param   integer   id from the user_info_field table
     */
    function set_fieldid($fieldid) {
        $this->fieldid = $fieldid;
    }

    /**
     * Accessor method: Load the field record and user data associated with the
     * object's fieldid and userid
     */
    function load_data() {
        /// Load the field object
        if (($this->fieldid == 0) or (!($field = get_record('user_info_field', 'id', $this->fieldid)))) {
            $this->field = NULL;
            $this->inputname = '';
        } else {
            $this->field = $field;
            $this->inputname = 'profile_field_'.$field->shortname;
        }

        if (!empty($this->field)) {
            if ($datafield = get_field('user_info_data', 'data', 'userid', $this->userid, 'fieldid', $this->fieldid)) {
                $this->data = $datafield;
            } else {
                $this->data = $this->field->defaultdata;
            }
        } else {
            $this->data = NULL;
        }
    }

    /**
     * Check if the field data is visible to the current user
     * @return  boolean
     */
    function is_visible() {
        global $USER;

        switch ($this->field->visible) {
            case PROFILE_VISIBLE_ALL:
                return true;
            case PROFILE_VISIBLE_PRIVATE:
                return ($this->userid == $USER->id);
            default:
                return has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID));
        }
    }

    /**
     * Check if the field is required on the edit profile page
     * @return   boolean
     */
    function is_required() {
        return (boolean)$this->field->required;
    }

    /**
     * Check if the field is locked on the edit profile page
     * @return   boolean
     */
    function is_locked() {
        return (boolean)$this->field->locked;
    }

} /// End of class definition


/***** General purpose functions for customisable user profiles *****/

function profile_load_data(&$user) {
    global $CFG;

    if ($fields = get_records_select('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $user->id);
            $formfield->edit_load_user_data($user);
        }
    }
}

/**
 * Print out the customisable categories and fields for a users profile
 * @param  object   instance of the moodleform class
 */
function profile_definition(&$mform) {
    global $CFG;

    if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        foreach ($categories as $category) {
            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                $mform->addElement('header', 'category_'.$category->id, format_string($category->name));
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id);
                    $formfield->edit_field($mform);

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
            $formfield = new $newfield($field->id, $usernew->id);
            $err += $formfield->edit_validate_field($usernew);
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
            $formfield = new $newfield($field->id, $usernew->id);
            $formfield->edit_save_data($usernew);
        }
    }
}

function profile_display_fields($userid) {
    global $CFG, $USER;

    if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        foreach ($categories as $category) {
            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id, $userid);
                    if ($formfield->is_visible() and ($formfield->data !== NULL)) {
                        print_row(s($formfield->field->name.':'), $formfield->display_data());
                    }
                }
            }
        }
    }
}




?>
