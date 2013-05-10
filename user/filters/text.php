<?php

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter for text fields.
 */
class user_filter_text extends user_filter_type {
    var $_field;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field user table filed name
     */
    function user_filter_text($name, $label, $advanced, $field) {
        parent::user_filter_type($name, $label, $advanced);
        $this->_field = $field;
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain','filters'),
                     2 => get_string('isequalto','filters'),
                     3 => get_string('startswith','filters'),
                     4 => get_string('endswith','filters'),
                     5 => get_string('isempty','filters'));
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name.'_op', null, $this->getOperators());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $mform->setType($this->_name, PARAM_RAW);
        $mform->disabledIf($this->_name, $this->_name.'_op', 'eq', 5);
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
        $field    = $this->_name;
        $operator = $field.'_op';

        if (array_key_exists($operator, $formdata)) {
            if ($formdata->$operator != 5 and $formdata->$field == '') {
                // no data - no change except for empty filter
                return false;
            }
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            return array('operator' => (int)$formdata->$operator, 'value' =>  $fieldvalue);
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    function get_sql_filter($data) {
        global $DB;
        static $counter = 0;
        $name = 'ex_text'.$counter++;

        $operator = $data['operator'];
        $value    = $data['value'];
        $field    = $this->_field;

        $params = array();

        if ($operator != 5 and $value === '') {
            return '';
        }

        switch($operator) {
            case 0: // contains
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // does not contain
                $res = $DB->sql_like($field, ":$name", false, false, true);
                $params[$name] = "%$value%";
                break;
            case 2: // equal to
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // starts with
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // ends with
                $res = $DB->sql_like($field, ":$name", false, false);
                $params[$name] = "%$value";
                break;
            case 5: // empty
                $res = "$field = :$name";
                $params[$name] = '';
                break;
            default:
                return '';
        }
        return array($res, $params);
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    function get_label($data) {
        $operator  = $data['operator'];
        $value     = $data['value'];
        $operators = $this->getOperators();

        $a = new stdClass();
        $a->label    = $this->_label;
        $a->value    = '"'.s($value).'"';
        $a->operator = $operators[$operator];


        switch ($operator) {
            case 0: // contains
            case 1: // doesn't contain
            case 2: // equal to
            case 3: // starts with
            case 4: // ends with
                return get_string('textlabel', 'filters', $a);
            case 5: // empty
                return get_string('textlabelnovalue', 'filters', $a);
        }

        return '';
    }
}
