<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * User filter based on values of custom profile fields.
 */
class user_filter_profilefield extends user_filter_type {
    /**
     * operator used for comparison of data
     */
    var $_operator;
    /**
     * profile field to look at (0 - all fields)
     */
    var $_profile_field;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param string $value the value of the profile field (used for filtering data)
     * @param int $profile_field id of the profile field to look in
     * @param int $operator code of the comparison operator
     */
    function user_filter_profilefield($name, $label, $field='id', $value=null, $profile_field=0, $operator=0) {
        parent::user_filter_type($name, $label, $field, $value);
        $this->_operator = $operator;
        $this->_profile_field = $profile_field;
    }
    
    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(
            get_string('contains', 'filters'),
            get_string('doesnotcontain','filters'),
            get_string('isequalto','filters'),
            get_string('startswith','filters'),
            get_string('endswith','filters'),
            get_string('isempty','filters'),
            get_string('isnotdefined','filters'),
            get_string('isdefined','filters'),
        );
    }
    
    /**
     * Returns an array of custom profile fields
     * @return array of profile fields
     */
    function getProfileFields() {
        $fields =& get_records_select('user_info_field', '', 'shortname', 'id,shortname');
        if(empty($fields)) {
            return null;
        }
        $res[0] = get_string('anyfield','filters');
        foreach($fields as $k=>$v) {
            $res[$k] = $v->shortname;
        }
        return $res;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $profile_fields =& $this->getProfileFields();
        if(empty($profile_fields)) {
            return;
        }
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name . '_fld', null, $profile_fields);
        $objs[] =& $mform->createElement('select', $this->_name . '_op', null, $this->getOperators());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('profilefield','','filters'));
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $field = $this->_name;
        $operator = $field . '_op';
        $profile_field = $field . '_fld';
        $this->_value = (string)@$formdata->$field;
        $this->_operator = (int)@$formdata->$operator;
        $this->_profile_field = (int)@$formdata->$profile_field;
    }

    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        global $CFG;
        $where = '';
        $op = ' IN ';
        switch($this->_operator) {
        case 0: // contains
            if(empty($this->_value)) {
                return null;
            }
            $where = 'data ' . sql_ilike(). ' "%' . $this->_value . '%"';
            break;
        case 1: // does not contain
            if(empty($this->_value)) {
                return null;
            }
            $where = 'data NOT ' . sql_ilike(). ' "%' . $this->_value . '%"';
            break;
        case 2: // equal to
            if(empty($this->_value)) {
                return null;
            }
            $where = 'data="' . $this->_value . '"';
            break;
        case 3: // starts with
            if(empty($this->_value)) {
                return null;
            }
            $where = 'data ' . sql_ilike(). ' "' . $this->_value . '%"';
            break;
        case 4: // ends with 
            if(empty($this->_value)) {
                return null;
            }
            $where = 'data ' . sql_ilike(). ' "%' . $this->_value . '"';
            break;
        case 5: // empty
            $where = 'data=""';
            break;
        case 6: // is not defined
            $op = ' NOT IN ';
            break;
        case 7: // is defined
            break;
        }
        if(!empty($this->_profile_field)) {
            if(!empty($where)) {
                $where = ' AND ' . $where;
            }
            $where = 'fieldid=' . $this->_profile_field . $where;
        }
        if(!empty($where)) {
            $where = ' WHERE ' . $where;
        }
        return $this->_field . $op . "(SELECT userid FROM {$CFG->prefix}user_info_data" . $where . ')';
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        $res = '';
        $operators =& $this->getOperators();
        $profilefields =& $this->getProfileFields();
        switch($this->_operator) {
        case 0: // contains
        case 1: // doesn't contain
        case 2: // equal to
        case 3: // starts with
        case 4: // ends with 
            $res = $this->_label . ': '. $profilefields[$this->_profile_field] . ' ' . $operators[$this->_operator]. ' "' . stripslashes($this->_value) . '"';
            break;
        case 5: // empty
        case 6: // is not defined
        case 7: // is defined
            $res = $this->_label . ': '. $profilefields[$this->_profile_field] . ' ' . $operators[$this->_operator];
            break;
        }
        return $res;
    }
}
