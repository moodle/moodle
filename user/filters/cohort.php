<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter for cohort membership.
 */
class user_filter_cohort extends user_filter_type {
    /**
     * Constructor
     * @param boolean $advanced advanced form element flag
     */
    function user_filter_cohort($advanced) {
        parent::user_filter_type('cohort', get_string('idnumber', 'core_cohort'), $advanced);
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
                     4 => get_string('endswith','filters'));
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
        $mform->setDefault($this->_name.'_op', 2);
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
            if ($formdata->$field == '') {
                return false;
            }
            return array('operator'=>(int)$formdata->$operator, 'value'=>$formdata->$field);
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
        $name = 'ex_cohort'.$counter++;

        $operator = $data['operator'];
        $value    = $data['value'];

        $params = array();

        if ($value === '') {
            return '';
        }

        switch($operator) {
            case 0: // contains
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // does not contain
                $res = $DB->sql_like('idnumber', ":$name", false, false, true);
                $params[$name] = "%$value%";
                break;
            case 2: // equal to
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // starts with
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // ends with
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "%$value";
                break;
            default:
                return '';
        }

        $sql = "id IN (SELECT userid
                         FROM {cohort_members}
                         JOIN {cohort} ON {cohort_members}.cohortid = {cohort}.id
                        WHERE $res)";

        return array($sql, $params);
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
        }

        return '';
    }
}
