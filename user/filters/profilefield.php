<?php

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * User filter based on values of custom profile fields.
 */
class user_filter_profilefield extends user_filter_type {

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     */
    function user_filter_profilefield($name, $label, $advanced) {
        parent::user_filter_type($name, $label, $advanced);
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function get_operators() {
        return array(0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain','filters'),
                     2 => get_string('isequalto','filters'),
                     3 => get_string('startswith','filters'),
                     4 => get_string('endswith','filters'),
                     5 => get_string('isempty','filters'),
                     6 => get_string('isnotdefined','filters'),
                     7 => get_string('isdefined','filters'));
    }

    /**
     * Returns an array of custom profile fields
     * @return array of profile fields
     */
    function get_profile_fields() {
        global $DB;
        if (!$fields = $DB->get_records('user_info_field', null, 'shortname', 'id,shortname')) {
            return null;
        }
        $res = array(0 => get_string('anyfield', 'filters'));
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
        $profile_fields = $this->get_profile_fields();
        if (empty($profile_fields)) {
            return;
        }
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name.'_fld', null, $profile_fields);
        $objs[] =& $mform->createElement('select', $this->_name.'_op', null, $this->get_operators());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $mform->setType($this->_name, PARAM_RAW);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    function check_data($formdata) {
        $profile_fields = $this->get_profile_fields();

        if (empty($profile_fields)) {
            return false;
        }

        $field    = $this->_name;
        $operator = $field.'_op';
        $profile  = $field.'_fld';

        if (array_key_exists($profile, $formdata)) {
            if ($formdata->$operator < 5 and $formdata->$field === '') {
                return false;
            }

            return array('value'    => (string)$formdata->$field,
                         'operator' => (int)$formdata->$operator,
                         'profile'  => (int)$formdata->$profile);
        }
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    function get_sql_filter($data) {
        global $CFG, $DB;
        static $counter = 0;
        $name = 'ex_profilefield'.$counter++;

        $profile_fields = $this->get_profile_fields();
        if (empty($profile_fields)) {
            return '';
        }

        $profile  = $data['profile'];
        $operator = $data['operator'];
        $value    = $data['value'];

        $params = array();
        if (!array_key_exists($profile, $profile_fields)) {
            return array('', array());
        }

        $where = "";
        $op = " IN ";

        if ($operator < 5 and $value === '') {
            return '';
        }

        switch($operator) {
            case 0: // contains
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // does not contain
                $where = $DB->sql_like('data', ":$name", false, false, true);
                $params[$name] = "%$value%";
                break;
            case 2: // equal to
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // starts with
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // ends with
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "%$value";
                break;
            case 5: // empty
                $where = "data = :$name";
                $params[$name] = "";
                break;
            case 6: // is not defined
                $op = " NOT IN "; break;
            case 7: // is defined
                break;
        }
        if ($profile) {
            if ($where !== '') {
                $where = " AND $where";
            }
            $where = "fieldid=$profile $where";
        }
        if ($where !== '') {
            $where = "WHERE $where";
        }
        return array("id $op (SELECT userid FROM {user_info_data} $where)", $params);
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    function get_label($data) {
        $operators      = $this->get_operators();
        $profile_fields = $this->get_profile_fields();

        if (empty($profile_fields)) {
            return '';
        }

        $profile  = $data['profile'];
        $operator = $data['operator'];
        $value    = $data['value'];

        if (!array_key_exists($profile, $profile_fields)) {
            return '';
        }

        $a = new stdClass();
        $a->label    = $this->_label;
        $a->value    = $value;
        $a->profile  = $profile_fields[$profile];
        $a->operator = $operators[$operator];

        switch($operator) {
            case 0: // contains
            case 1: // doesn't contain
            case 2: // equal to
            case 3: // starts with
            case 4: // ends with
                return get_string('profilelabel', 'filters', $a);
            case 5: // empty
            case 6: // is not defined
            case 7: // is defined
                return get_string('profilelabelnovalue', 'filters', $a);
        }
        return '';
    }
}
