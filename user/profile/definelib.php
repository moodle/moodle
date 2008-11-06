<?php  //$Id$

class profile_define_base {

    /**
     * Prints out the form snippet for creating or editing a profile field
     * @param   object   instance of the moodleform class
     */
    function define_form(&$form) {
        $form->addElement('header', '_commonsettings', get_string('profilecommonsettings', 'admin'));
        $this->define_form_common($form);

        $form->addElement('header', '_specificsettings', get_string('profilespecificsettings', 'admin'));
        $this->define_form_specific($form);
    }

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field common to all data types
     * @param   object   instance of the moodleform class
     */
    function define_form_common(&$form) {

        $strrequired = get_string('required');

        $form->addElement('text', 'shortname', get_string('profileshortname', 'admin'), 'maxlength="100" size="25"');
        $form->addRule('shortname', $strrequired, 'required', null, 'client');
        $form->setType('shortname', PARAM_ALPHANUM);

        $form->addElement('text', 'name', get_string('profilename', 'admin'), 'size="50"');
        $form->addRule('name', $strrequired, 'required', null, 'client');
        $form->setType('name', PARAM_MULTILANG);

        $form->addElement('htmleditor', 'description', get_string('profiledescription', 'admin'));
        $form->setHelpButton('description', array('text', get_string('helptext')));

        $form->addElement('selectyesno', 'required', get_string('profilerequired', 'admin'));

        $form->addElement('selectyesno', 'locked', get_string('profilelocked', 'admin'));

        $form->addElement('selectyesno', 'forceunique', get_string('profileforceunique', 'admin'));
        
        $form->addElement('selectyesno', 'signup', get_string('profilesignup', 'admin'));

        $choices = array();
        $choices[PROFILE_VISIBLE_NONE]    = get_string('profilevisiblenone', 'admin');
        $choices[PROFILE_VISIBLE_PRIVATE] = get_string('profilevisibleprivate', 'admin');
        $choices[PROFILE_VISIBLE_ALL]     = get_string('profilevisibleall', 'admin');
        $form->addElement('select', 'visible', get_string('profilevisible', 'admin'), $choices);
        $form->setHelpButton('visible', array('profilevisible', get_string('profilevisible','admin')));
        $form->setDefault('visible', PROFILE_VISIBLE_ALL);

        $choices = profile_list_categories();
        $form->addElement('select', 'categoryid', get_string('profilecategory', 'admin'), $choices);
    }

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field specific to the current data type
     * @param   object   instance of the moodleform class
     */
    function define_form_specific(&$form) {
        /// do nothing - overwrite if necessary
    }

    /**
     * Validate the data from the add/edit profile field form.
     * Generally this method should not be overwritten by child
     * classes.
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function define_validate($data, $files) {

        $data = (object)$data;
        $err = array();

        $err += $this->define_validate_common($data, $files);
        $err += $this->define_validate_specific($data, $files);

        return $err;
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is common to all data types. Generally this method
     * should not be overwritten by child classes.
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function define_validate_common($data, $files) {

        global $USER;

        $err = array();

        /// Check the shortname was not truncated by cleaning
        if (empty($data->shortname)) {
            $err['shortname'] = get_string('required');

        } else {
        /// Fetch field-record from DB
            $field = get_record('user_info_field', 'shortname', $data->shortname);
        /// Check the shortname is unique
            if ($field and $field->id <> $data->id) {
                $err['shortname'] = get_string('profileshortnamenotunique', 'admin');

        /// Shortname must also be unique compared to the standard user fields
            } else if (!$field and isset($USER->{$data->shortname})) {
                $err['shortname'] = get_string('profileshortnamenotunique', 'admin');
            }
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
    function define_validate_specific($data, $files) {
        /// do nothing - overwrite if necessary
        return array();
    }

    /**
     * Alter form based on submitted or existing data
     * @param   object   form
     */
    function define_after_data(&$mform) {
        /// do nothing - overwrite if necessary
    }

    /**
     * Add a new profile field or save changes to current field
     * @param   object   data from the add/edit profile field form
     * @return  boolean  status of the insert/update record
     */
    function define_save($data) {

        $data = $this->define_save_preprocess($data); /// hook for child classes

        $old = false;
        if (!empty($data->id)) {
            $old = get_record('user_info_field', 'id', $data->id);
        }

        /// check to see if the category has changed
        if (!$old or $old->categoryid != $data->categoryid) {
            $data->sortorder = count_records_select('user_info_field', 'categoryid='.$data->categoryid) + 1;
        }


        if (empty($data->id)) {
            unset($data->id);
            if (!$data->id = insert_record('user_info_field', $data)) {
                error('Error creating new field');
            }
        } else {
            if (!update_record('user_info_field', $data)) {
                error('Error updating field');
            }
        }
    }

    /**
     * Preprocess data from the add/edit profile field form
     * before it is saved. This method is a hook for the child
     * classes to overwrite.
     * @param   object   data from the add/edit profile field form
     * @return  object   processed data object
     */
    function define_save_preprocess($data) {
        /// do nothing - overwrite if necessary
        return $data;
    }

}



/**
 * Reorder the profile fields within a given category starting
 * at the field at the given startorder
 */
function profile_reorder_fields() {
    if ($categories = get_records_select('user_info_category')) {
        foreach ($categories as $category) {
            $i = 1;
            if ($fields = get_records_select('user_info_field', 'categoryid='.$category->id, 'sortorder ASC')) {
                foreach ($fields as $field) {
                    $f = new object();
                    $f->id = $field->id;
                    $f->sortorder = $i++;
                    update_record('user_info_field', $f);
                }
            }
        }
    }
}

/**
 * Reorder the profile categoriess starting at the category
 * at the given startorder
 */
function profile_reorder_categories() {
    $i = 1;
    if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        foreach ($categories as $cat) {
            $c = new object();
            $c->id = $cat->id;
            $c->sortorder = $i++;
            update_record('user_info_category', $c);
        }
    }
}

/**
 * Delete a profile category
 * @param   integer   id of the category to be deleted
 * @return  boolean   success of operation
 */
function profile_delete_category($id) {
    /// Retrieve the category
    if (!$category = get_record('user_info_category', 'id', $id)) {
        error('Incorrect category id');
    }

    if (!$categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        error('Error no categories!?!?');
    }

    unset($categories[$category->id]);

    if (!count($categories)) {
        return; //we can not delete the last category
    }

    /// Does the category contain any fields
    if (count_records('user_info_field', 'categoryid', $category->id)) {
        if (array_key_exists($category->sortorder-1, $categories)) {
            $newcategory = $categories[$category->sortorder-1];
        } else if (array_key_exists($category->sortorder+1, $categories)) {
            $newcategory = $categories[$category->sortorder+1];
        } else {
            $newcategory = reset($categories); // get first category if sortorder broken
        }

        $sortorder = count_records('user_info_field', 'categoryid', $newcategory->id) + 1;

        if ($fields = get_records_select('user_info_field', 'categoryid='.$category->id, 'sortorder ASC')) {
            foreach ($fields as $field) {
                $f = new object();
                $f->id = $field->id;
                $f->sortorder = $sortorder++;
                $f->categoryid = $newcategory->id;
                update_record('user_info_field', $f);
                echo "<pre>";var_dump($f);echo"</pre>";
            }
        }
    }

    /// Finally we get to delete the category
    if (!delete_records('user_info_category', 'id', $category->id)) {
        error('Error while deliting category');
    }
    profile_reorder_categories();
    return true;
}


function profile_delete_field($id) {

    /// Remove any user data associated with this field
    if (!delete_records('user_info_data', 'fieldid', $id)) {
        error('Error deleting custom field data');
    }

    /// Try to remove the record from the database
    delete_records('user_info_field', 'id', $id);

    /// Reorder the remaining fields in the same category
    profile_reorder_fields();
}

/**
 * Change the sortorder of a field
 * @param   integer   id of the field
 * @param   string    direction of move
 * @return  boolean   success of operation
 */
function profile_move_field($id, $move) {
    /// Get the field object
    if (!$field = get_record('user_info_field', 'id', $id, '', '', '', '', 'id, sortorder, categoryid')) {
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
    if ($swapfield = get_record('user_info_field', 'categoryid', $field->categoryid, 'sortorder', $neworder, '', '', 'id, sortorder')) {

        /// Swap the sortorders
        $swapfield->sortorder = $field->sortorder;
        $field->sortorder     = $neworder;

        /// Update the field records
        update_record('user_info_field', $field);
        update_record('user_info_field', $swapfield);
    }

    profile_reorder_fields();
}

/**
 * Change the sortorder of a category
 * @param   integer   id of the category
 * @param   string    direction of move
 * @return  boolean   success of operation
 */
function profile_move_category($id, $move) {
    /// Get the category object
    if (!($category = get_record('user_info_category', 'id', $id, '', '', '', '', 'id, sortorder'))) {
        return false;
    }

    /// Count the number of categories
    $categorycount = count_records('user_info_category');

    /// Calculate the new sortorder
    if ( ($move == 'up') and ($category->sortorder > 1)) {
        $neworder = $category->sortorder - 1;
    } elseif ( ($move == 'down') and ($category->sortorder < $categorycount)) {
        $neworder = $category->sortorder + 1;
    } else {
        return false;
    }

    /// Retrieve the category object that is currently residing in the new position
    if ($swapcategory = get_record('user_info_category', 'sortorder', $neworder, '', '', '', '', 'id, sortorder')) {

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
 * Retrieve a list of all the available data types
 * @return   array   a list of the datatypes suitable to use in a select statement
 */
function profile_list_datatypes() {
    global $CFG;

    $datatypes = array();

    if ($dirlist = get_directory_list($CFG->dirroot.'/user/profile/field', '', false, true, false)) {
        foreach ($dirlist as $type) {
            $datatypes[$type] = get_string('profilefieldtype'.$type, 'profilefield_'.$type);
            if (strpos($datatypes[$type], '[[') !== false) {
                $datatypes[$type] = get_string('profilefieldtype'.$type, 'admin');
            }
        }
    }
    asort($datatypes);

    return $datatypes;
}

/**
 * Retrieve a list of categories and ids suitable for use in a form
 * @return   array
 */
function profile_list_categories() {
    if (!$categories = get_records_select_menu('user_info_category', '', 'sortorder ASC', 'id, name')) {
        $categories = array();
    }
    return $categories;
}


/// Are we adding or editing a cateogory?
function profile_edit_category($id, $redirect) {
    global $CFG;

    require_once('index_category_form.php');
    $categoryform = new category_form();

    if ($category = get_record('user_info_category', 'id', $id)) {
        $categoryform->set_data($category);
    }

    if ($categoryform->is_cancelled()) {
        redirect($redirect);
    } else {
        if ($data = $categoryform->get_data()) {
            if (empty($data->id)) {
                unset($data->id);
                $data->sortorder = count_records('user_info_category') + 1;
                if (!insert_record('user_info_category', $data, false)) {
                    error('There was a problem adding the record to the database');
                }
            } else {
                if (!update_record('user_info_category', $data)) {
                    error('There was a problem updating the record in the database');
                }
            }
            profile_reorder_categories();
            redirect($redirect);

        }

        if (empty($id)) {
            $strheading = get_string('profilecreatenewcategory', 'admin');
        } else {
            $strheading = get_string('profileeditcategory', 'admin', format_string($category->name));
        }

        /// Print the page
        admin_externalpage_print_header();
        print_heading($strheading);
        $categoryform->display();
        admin_externalpage_print_footer();
        die;
    }

}

function profile_edit_field($id, $datatype, $redirect) {
    global $CFG;

    if (!$field = get_record('user_info_field', 'id', $id)) {
        $field = new object();
        $field->datatype = $datatype;
    }

    require_once('index_field_form.php');
    $fieldform = new field_form(null, $field->datatype);
    $fieldform->set_data($field);

    if ($fieldform->is_cancelled()) {
        redirect($redirect);

    } else {
        if ($data = $fieldform->get_data()) {
            require_once($CFG->dirroot.'/user/profile/field/'.$datatype.'/define.class.php');
            $newfield = 'profile_define_'.$datatype;
            $formfield = new $newfield();
            $formfield->define_save($data);
            profile_reorder_fields();
            profile_reorder_categories();
            redirect($redirect);
        }

        $datatypes = profile_list_datatypes();

        if (empty($id)) {
            $strheading = get_string('profilecreatenewfield', 'admin', $datatypes[$datatype]);
        } else {
            $strheading = get_string('profileeditfield', 'admin', $field->name);
        }

        /// Print the page
        admin_externalpage_print_header();
        print_heading($strheading);
        $fieldform->display();
        admin_externalpage_print_footer();
        die;
    }
}

?>
