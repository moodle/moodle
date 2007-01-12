<?php //$Id$

/// Some constants

define ('PROFILE_REQUIRED_YES',    '1');
define ('PROFILE_REQUIRED_NO',     '0');

define ('PROFILE_VISIBLE_ALL',     '2');
define ('PROFILE_VISIBLE_PRIVATE', '1');
define ('PROFILE_VISIBLE_NONE',    '0');

define ('PROFILE_LOCKED_YES',      '1');
define ('PROFILE_LOCKED_NO',       '0');


/**
 * Base class for the cusomisable profile fields.
 */
class profile_field_base {

    var $datatype  = '';   /// data type of this field
    var $fieldid   = 0;    /// id from user_info_field table
    var $dataid    = 0;    /// id from user_info_data table
    var $userid    = 0;    /// id from the user table
    var $field     = null; /// a copy of the field information
    var $fieldname = '';   /// form name of the field

    /**
     * Constructor method.
     * @param   integer   id of the profile from the user_info_field table
     * @param   integer   id of the user for whom we are displaying data
     */
    function profile_field_base($fieldid=0, $userid=0) {
        global $USER;

        /// Set the various properties for this class ///
        
        $this->fieldid = $fieldid;

        /// If $userid is empty, assume the current user
        $this->userid = (empty($userid)) ? $USER->id : $userid;

        /// Set $field
        if ( empty($fieldid) or (($field = get_record('user_info_field', 'id', $fieldid)) === false) ) {
            $field = null;
        }
        $this->field = $field;

        $this->set_data_type();
        
        /// If the child class hasn't implemented it's own set_data_type method
        /// then we can get the type from the $field object if it exists
        if (!empty($field) and ($this->datatype == 'unknown')) {
            $this->datatype = $field->datatype;
        }

        if ($field) {
            $this->fieldname = $field->shortname;
        } else { /// we are creating a new profile field
            $this->fieldname = 'new_'.$this->datatype;
        }
        
        /// We can set $dataid from $fieldid and $userid (if it exists)
        if (!empty($fieldid) and !empty($userid)) {
            if (($dataid = get_field('user_info_data', 'id', 'fieldid', $fieldid, 'userid', $userid)) === false) {
                $dataid = 0;
            }
        } else {
            $dataid = 0;
        }
        $this->dataid = $dataid;

        /// End of setting the properties ///


        /// Post setup processing
        $this->init();

    }


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
     * @param   object   instance of the moodleform class
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

    /**
     * Locks (disables) the field if required in the form object
     * @param   object   instance of the moodleform class
     */
    function display_field_lock (&$form) {
        if ($this->_is_locked()) {
            $form->freeze($this->fieldname);
        }
    }

    /**
     * Sets the default data for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function display_field_default(&$form) {
        if (!($default = get_field('user_info_data', 'data', 'userid', $this->userid, 'fieldid', $this->field->id))) {
            $default = $this->field->defaultdata;
        }
        if (!empty($default)) {
            $form->setDefault($this->fieldname, $default);
        }
    }

    /**
     * Sets the required flag for the field in the form object
     * @param   object   instance of the moodleform class
     */
    function display_field_required(&$form) {
        if ( $this->_is_required() and !$this->_is_locked() ) {
            $form->addRule($this->fieldname, get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * Hook for child classes to perform any post-setup processes
     */
    function init() {
        /// do nothing - overwrite if necessary
    }


    /**
     * Prints out the form snippet for creating or editing a profile field
     * @param   object   instance of the moodleform class
     */
    function edit_field (&$form) {
        $form->addElement('header', '_commonsettings', get_string('profilecommonsettings'));
        $this->edit_field_common($form);
        
        $form->addElement('header', '_specificsettings', get_string('profilespecificsettings'));
        $this->edit_field_specific($form);
    }

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field common to all data types
     * @param   object   instance of the moodleform class
     */
    function edit_field_common (&$form) {

        $strrequired = get_string('required');
        
        $form->addElement('text', 'shortname', get_string('profileshortname'), 'maxlength="100" size="30"');
        $form->setType('shortname', PARAM_ALPHANUM);
        $form->addRule('shortname', $strrequired, 'required', null, 'client');

        $form->addElement('text', 'name', get_string('profilename'), 'size="30"');
        $form->setType('name', PARAM_MULTILANG);
        $form->addRule('name', $strrequired, 'required', null, 'client');

        $form->addElement('htmleditor', 'description', get_string('profiledescription'));
        $form->setType('description', PARAM_MULTILANG);
        $form->setHelpButton('description', array('text', get_string('helptext')));

        $form->addElement('selectyesno', 'required', get_string('profilerequired'));
        $form->setType('required', PARAM_BOOL);

        $form->addElement('selectyesno', 'locked', get_string('profilelocked'));
        $form->setType('locked', PARAM_BOOL);

        unset($choices);
        $choices[0] = get_string('profilevisiblenone');
        $choices[1] = get_string('profilevisibleprivate');
        $choices[2] = get_string('profilevisibleall');
        $form->addElement('select', 'visible', get_string('profilevisible'), $choices);
        $form->setType('visible', PARAM_INT);

        unset($choices);
        $choices = profile_list_categories();
        $form->addElement('select', 'categoryid', get_string('profilecategory'), $choices);
        $form->setType('categoryid', PARAM_INT);
    }

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field specific to the current data type
     * @param   object   instance of the moodleform class
     */
    function edit_field_specific (&$form) {
        /// do nothing - overwrite if necessary
    }

    /**
     * Validate the data from the add/edit profile field form.
     * Generally this method should not be overwritten by child
     * classes.
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function edit_validate ($data) {

        $data = (object)$data;
        $err = array();
        
        $err += $this->edit_validate_common($data);
        $err += $this->edit_validate_specific($data);

        return $err;
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is common to all data types. Generally this method
     * should not be overwritten by child classes.
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function edit_validate_common ($data) {
        $err = array();
        
        /// Check the shortname is unique
        if (($field = get_record('user_info_field', 'shortname', $data->shortname)) and ($field->id <> $data->id)) {
        //if (record_exists_select('user_info_field', 'shortname='.$data->shortname.' AND id<>'.$data->id)) {
            $err['shortname'] = get_string('profileshortnamenotunique');
        }

        /// No further checks necessary as the form class will take care of it

        return $err;
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is specific to the current data type
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function edit_validate_specific ($data) {
        $err = array();
        
        /// do nothing - overwrite if necessary

        return $err;
    }

    /**
     * Add a new profile field or save changes to current field
     * @param   object   data from the add/edit profile field form
     * @return  boolean  status of the insert/update record
     */
    function edit_save ($data) {

        /// check to see if the category has changed
        if ( (isset($this->field->categoryid) and ($this->field->categoryid != $data->categoryid)) or ($data->id == 0)) {
            /// Set the sortorder for the field in the new category
            $data->sortorder = count_records_select('user_info_field', 'categoryid='.$data->categoryid) + 1;
        }
        
        $data = $this->edit_save_preprocess($data); /// hook for child classes
        
        if ($data->id == 0) {
            unset($data->id);
            if ($success = insert_record('user_info_field', $data)) {
                $data->id = $success;
                $success = true;
            } else {
                $success = false;
            }
        } else {
            $success = update_record('user_info_field', $data);
        }

        /// Store the new information in this objects properties
        if ($success) {
            if (isset($this->field->categoryid) and ($this->field->categoryid != $data->categoryid)) {
                /// Change the sortorder of the other fields in the old category
                profile_reorder_fields($this->field->categoryid, $this->field->sortorder);
            }
            
            $this->field    = $data;
            $this->fieldid  = $data->id;

        }
        return $success;
    }

    /**
     * Preprocess data from the add/edit profile field form
     * before it is saved. This method is a hook for the child
     * classes to overwrite.
     * @param   object   data from the add/edit profile field form
     * @return  object   processed data object
     */
    function edit_save_preprocess ($data) {
        /// do nothing - overwrite if necessary
        return $data;
    }
    
    /**
     * Removes a profile field and all the user data associated with it
     */
    function edit_remove_field () {
    
        if (!empty($this->field->id)) {
            /// Remove the record from the database
            delete_records('user_info_field', 'id', $this->field->id);

            /// Reorder the remaining fields in the same category
            profile_reorder_fields($this->field->categoryid, $this->field->sortorder);

            /// Remove any user data associated with this field
            delete_records('user_info_data', 'fieldid', $this->field->id);
       }
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
     * Check if the field is locked to the current user
     * @return  boolean
     */
    function _is_locked() {
        return (    ($this->field->locked == PROFILE_LOCKED_YES)
                and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)));
    }

    /**
     * Check if the field is required
     * @return  boolean
     */
     function _is_required() {
        return ($this->field->required == PROFILE_REQUIRED_YES);
     }


} /// End of class definition


/***** General purpose functions for customisable user profiles *****/

/**
 * Retrieve a list of all the available data types
 * @return   array   a list of the datatypes suitable to use in a select statement
 */
function profile_list_datatypes() {
    global $CFG;

    $datatypes = array();

    if ($dirlist = get_directory_list($CFG->dirroot.'/user/profile/field', '', false, true, false)) {
        foreach ($dirlist as $type) {
            $datatypes[$type] = $type;
        }
    }
    return $datatypes;
//    return get_directory_list($CFG->dirroot.'/user/profile/field', '', false, true, false);
}

/**
 * Change the sortorder of a field
 * @param   integer   id of the field
 * @param   string    direction of move
 * @return  boolean   success of operation
 */
function profile_move_field ($id, $move='down') {
    /// Get the field object
    if (!($field = get_record('user_info_field', 'id', $id))) {
        return false;
    }
    /// Count the number of fields in this category
    $fieldcount = count_records_select('user_info_field', 'categoryid='.$field->categoryid);

    /// Calculate the new sortorder
    if ( ($move == 'up') and ($field->sortorder > 1)) {
        $neworder = $field->sortorder - 1;
    } elseif ( ($move == 'down') and ($field->sortorder < $fieldcount)) {
        $neworder = $field->sortorder + 1;
    } else {
        return false;
    }

    /// Retrieve the field object that is currently residing in the new position
    if ($swapfield = get_record('user_info_field', 'categoryid', $field->categoryid, 'sortorder', $neworder)) {

        /// Swap the sortorders
        $swapfield->sortorder = $field->sortorder;
        $field->sortorder     = $neworder;

        /// Update the field records
        if (update_record('user_info_field', $field) and update_record('user_info_field', $swapfield)) {
            return true;
        }
    }

    return false;
}

/**
 * Change the sortorder of a category
 * @param   integer   id of the category
 * @param   string    direction of move
 * @return  boolean   success of operation
 */
function profile_move_category ($id, $move='down') {
    /// Get the category object
    if (!($category = get_record('user_info_category', 'id', $id))) {
        return false;
    }

    /// Count the number of categories
    $categorycount = count_records_select('user_info_category', '1');

    /// Calculate the new sortorder
    if ( ($move == 'up') and ($category->sortorder > 1)) {
        $neworder = $category->sortorder - 1;
    } elseif ( ($move == 'down') and ($category->sortorder < $categorycount)) {
        $neworder = $category->sortorder + 1;
    } else {
        return false;
    }

    /// Retrieve the category object that is currently residing in the new position
    if ($swapcategory = get_record('user_info_category', 'sortorder', $neworder)) {

        /// Swap the sortorders
        $swapcategory->sortorder = $category->sortorder;
        $category->sortorder     = $neworder;

        /// Update the category records
        if (update_record('user_info_category', $category) and update_record('user_info_category', $swapcategory)) {
            return true;
        }
    }

    return false;
}


/**
 * Retrieve a list of categories and ids suitable for use in a form
 * @return   array
 */
function profile_list_categories() {
    if ( !($categories = get_records_select_menu('user_info_category', '1', 'sortorder ASC', 'id, name')) ) {
        $categories = array();
    }
    return $categories;
}

/**
 * Delete a profile category
 * @param   integer   id of the category to be deleted
 * @return  boolean   success of operation
 */
function profile_delete_category ($id) {
    /// Retrieve the category
    if (!($category = get_record('user_info_category', 'id', $id))) {
        return false;
    }

    /// Retrieve the next category up
    if ( !($newcategory = get_record('user_info_category', 'sortorder', ($category->sortorder - 1))) ) {

        /// Retrieve the next category down
        if (!($newcategory = get_record('user_info_category', 'sortorder', ($category->sortorder + 1))) ) {

            /// We cannot find any other categories next to current one:
            /// 1. The sortorder values are incongruous which means a bug somewhere
            /// 2. We are the only category => cannot delete this category!
            return false;
        }
    }

    /// Does the category contain any fields
    if (count_records('user_info_field', 'categoryid', $category->id) > 0) {
        /// Move fields to the new category
        $sortorder = count_records('user_info_field', 'categoryid', $newcategory->id);

        if ($fields = get_records('user_info_field', 'categoryid', $category->id)) {
            foreach ($fields as $field) {
                $sortorder++;
                $field->sortorder = $sortorder;
                $field->categoryid = $newcategory->id;
                update_record('user_info_field', $field);
            }
        }
    }

    /// Finally we get to delete the category
    if (delete_records('user_info_category', 'id', $category->id) !== false) {
        profile_reorder_categories();
        return true;
    } else {
        return false;
    }
}

/**
 * Reorder the profile fields within a given category starting
 * at the field at the given startorder
 * @param   integer   id of the category
 * @param   integer   starting order
 * @return  integer   number of fields reordered
 */
function profile_reorder_fields($categoryid, $startorder=1) {
    $count = 0;
    $sortorder = $startorder;
    
    if ($fields = get_records_select('user_info_field', 'categoryid='.$categoryid.' AND sortorder>='.$startorder, 'sortorder ASC')) {
        foreach ($fields as $field) {
            $field->sortorder = $sortorder;
            update_record('user_info_field', $field);
            $sortorder++;
            $count++;
        }
    }
    return $count;
}

/**
 * Reorder the profile categoriess starting at the category
 * at the given startorder
 * @param   integer   starting order
 * @return  integer   number of categories reordered
 */
function profile_reorder_categories($startorder=1) {
    $count = 0;
    $sortorder = $startorder;
    
    if ($categories = get_records_select('user_info_category', 'sortorder>='.$startorder, 'sortorder ASC')) {
        foreach ($categories as $cat) {
            $cat->sortorder = $sortorder;
            update_record('user_info_category', $cat);
            $sortorder++;
            $count++;
        }
    }
    return $count;
}

?>
