<?php //$Id$

/// Some constants

define ('PROFILE_REQUIRED_YES',    '1');
define ('PROFILE_REQUIRED_NO',     '0');

define ('PROFILE_VISIBLE_ALL',     '2');
define ('PROFILE_VISIBLE_PRIVATE', '1');
define ('PROFILE_VISIBLE_NONE',    '0');

define ('PROFILE_LOCKED_YES',      '1');
define ('PROFILE_LOCKED_NO',       '0');



class profile_field_base {

    var $datatype  = '';   /// data type of this field
    var $fieldid   = 0;    /// id from user_info_field table
    var $dataid    = 0;    /// id from user_info_data table
    var $userid    = 0;    /// id from the user table
    var $field     = null; /// a copy of the field information
    var $fieldname = '';   /// form name of the field


    /***** The following methods must be overwritten in the child classes *****/


    /**
     * Set the data type for this profile field
     */
    function set_data_type() {
        $this->datatype = 'unknown';
    }

    /**
     * Adds the profile field to the moodle form class
     * @param  form  instance of the moodleform class
     */
    function display_field_add(&$form) {
        /// Add the element to the form class
        /// By default we add a static field
        $form->addElement('static', $this->fieldname, $this->field->name, '');
    }
    
    /**
     * Validate the form field from profile page
     * @return  string  contains error message otherwise NULL
     **/
    function validate_profile_field ($data) {
        return NULL;
    }


    /***** The following methods may be overwritten by child classes *****/

    /**
     * Print out the form field in the profile page
     * @param  form  instance of the moodleform class
     * $return  boolean
     */
    function display_field (&$form) {
        if (empty($this->field)) {
            return false;
        }
        
        /// Check that this field is visible to current user
        if (!$this->_is_visible()) {
            return false;
        }

        $this->display_field_add($form);
        $this->display_field_lock($form);
        $this->display_field_default($form);
        $this->display_field_required($form);

        return true;
    }


    /// Constructor function
    function profile_field_base($fieldid=0, $userid=0, $dataid=0) {
        global $USER;

        /// If userid is empty, assume the current user
        if (empty($userid)) $userid = $USER->id;

        /// If dataid is not set we can set it from fieldid and userid
        if (empty($dataid) and !empty($fieldid) and !empty($userid)) {
            if (($dataid = get_field('user_info_data', 'id', 'fieldid', $fieldid, 'userid', $userid)) === false) {
                $dataid = 0;
            }
        }

        /// If the fieldid is set then we can automatically set the datatype
        if (!empty($fieldid)) {
            $field = get_record('user_info_field', 'id', $fieldid);
            $datatype = $field->datatype;
        } else {
            $datatype = 'unknown';
            $field = null;
        }

        $this->fieldid   = $fieldid;
        $this->userid    = $userid;
        $this->dataid    = $dataid;
        $this->datatype  = $datatype;
        $this->field     = $field;
        $this->fieldname = $this->datatype.'_'.$this->field->id; /// hack to avoid using integers as the field name

        /// Post setup processing
        $this->init();
    }

    /**
     * A hook for child classes to perform any post-setup processes
     */
    function init() {
        /// do nothing - overwrite if necessary
    }


    /// Prints out the form for creating a new profile field
    function edit_new_field () {

    }

    /// Removes a profile field and all data associated with it
    function edit_remove_field () {

    }

    /**
     * Validates the data coming from form
     * @param   mixed   data from the form
     * @return  string  error message
     */
    function validate_data ($data) {
        return '';
    }

    /**
     * Saves the data coming from form
     * @param   mixed   data coming from the form
     * @return  mixed   returns data id if success of db insert/update, false on fail, 0 if not permitted
     */
    function save_data ($data) {
        if ($this->_is_visible() and !$this->_is_locked()) { /// check that we have permission

            $data = $this->save_data_preprocess($data);
        
            unset($datarecord);
            $datarecord->userid  = $this->userid;
            $datarecord->fieldid = $this->fieldid;
            $datarecord->data    = $data;

            if ($this->dataid == 0) { /// inserting a new record
                if ($ret = insert_record('user_info_data', $datarecord)) {
                    $this->dataid = $ret;
                }
            } else {
                $datarecord->id = $this->dataid;
                if (update_record('user_info_data', $datarecord)) {
                    $ret = $this->dataid;
                } else {
                    $ret = false;
                }
            }

        } else {
            $ret = 0;
        }

        return $ret;
    }

    /**
     * Hook for child classess to process the data before it gets saved in database
     * @param   mixed
     * @return  mixed
     */
    function save_data_preprocess($data) {
        return $data;
    }
    
    function display_field_lock (&$form) {
        $form->disabledIf($this->fieldname, $this->_is_locked(), true);
    }

    function display_field_default(&$form) {
        if (!($default = get_field('user_info_data', 'data', 'userid', $this->userid, 'fieldid', $this->field->id))) {
            $default = (empty($this->field->defaultdata)) ? '' : $this->field->defaultdata;
        }
        $form->setDefault($this->fieldname, $default);
    }

    function display_field_required(&$form) {
        if ($this->_is_required()) {
            $form->addRule($this->fieldname, get_string('required'), 'required', null, 'client');
        }
    }

    /***** The following methods should never be overwritten *****/

    /**
     * Check if the current field is visible to the current user
     * @return  boolean
     */
    function _is_visible() {
        global $USER;
        /* Can we see this field? Choices are:
           1 - VISIBLE ALL;
           2 - VISIBLE PRIVATE - either we are viewing our own profile or we have required capability;
           3 - VISIBLE NONE - we have the required capability
         */
        return (      ($this->field->visible === PROFILE_VISIBLE_ALL)
                 or ( ($this->field->visible === PROFILE_VISIBLE_PRIVATE)
                      and ($this->userid == $USER->id) )
                 or has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)) );
    }

    /**
     * Check if the current field is locked to the current user
     * @return  boolean
     */
    function _is_locked() {
        return (    ($this->field->locked == PROFILE_LOCKED_YES)
                and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)));
    }

    /**
     * Check if the current field is required
     * @return  boolean
     */
     function _is_required() {
        return ($this->field->required == PROFILE_REQUIRED_YES);
     }


} /// End of class definition

?>
