<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cohort filter.
 *
 * @package   core_user
 * @category  user
 * @copyright 2011 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter for cohort membership.
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_cohort extends user_filter_type {
    /**
     * Constructor
     * @param boolean $advanced advanced form element flag
     */
    public function user_filter_cohort($advanced) {
        parent::user_filter_type('cohort', get_string('idnumber', 'core_cohort'), $advanced);
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    public function getOperators() {
        return array(0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain', 'filters'),
                     2 => get_string('isequalto', 'filters'),
                     3 => get_string('startswith', 'filters'),
                     4 => get_string('endswith', 'filters'),
                     5 => get_string('isempty', 'filters'));
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $objs = array();
        $objs[] = $mform->createElement('select', $this->_name.'_op', null, $this->getOperators());
        $objs[] = $mform->createElement('text', $this->_name, null);
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
    public function check_data($formdata) {
        $field    = $this->_name;
        $operator = $field.'_op';

        if (array_key_exists($operator, $formdata)) {
            if ($formdata->$operator != 5 and $formdata->$field == '') {
                // No data - no change except for empty filter.
                return false;
            }
            // If field value is set then use it, else it's null.
            $fieldvalue = null;
            if (isset($formdata->$field)) {
                $fieldvalue = $formdata->$field;
            }
            return array('operator' => (int)$formdata->$operator, 'value' => $fieldvalue);
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        global $DB;
        static $counter = 0;
        $name = 'ex_cohort'.$counter++;

        $operator = $data['operator'];
        $value    = $data['value'];

        $params = array();

        if ($value === '') {
            return '';
        }

        $not = '';
        switch($operator) {
            case 0: // Contains.
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // Does not contain.
                $not = 'NOT';
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 2: // Equal to.
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // Starts with.
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // Ends with.
                $res = $DB->sql_like('idnumber', ":$name", false, false);
                $params[$name] = "%$value";
                break;
            case 5: // Empty.
                $not = 'NOT';
                $res = '(idnumber IS NOT NULL AND idnumber <> :'.$name.')';
                $params[$name] = '';
                break;
            default:
                return '';
        }

        $sql = "id $not IN (SELECT userid
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
    public function get_label($data) {
        $operator  = $data['operator'];
        $value     = $data['value'];
        $operators = $this->getOperators();

        $a = new stdClass();
        $a->label    = $this->_label;
        $a->value    = '"'.s($value).'"';
        $a->operator = $operators[$operator];

        switch ($operator) {
            case 0: // Contains.
            case 1: // Doesn't contain.
            case 2: // Equal to.
            case 3: // Starts with.
            case 4: // Ends with.
                return get_string('textlabel', 'filters', $a);
            case 5: // Empty.
                return get_string('textlabelnovalue', 'filters', $a);
        }

        return '';
    }
}
