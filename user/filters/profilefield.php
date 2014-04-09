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
 * Profile field filter.
 *
 * @package   core_user
 * @category  user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * User filter based on values of custom profile fields.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_profilefield extends user_filter_type {

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     */
    public function user_filter_profilefield($name, $label, $advanced) {
        parent::user_filter_type($name, $label, $advanced);
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    public function get_operators() {
        return array(0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain', 'filters'),
                     2 => get_string('isequalto', 'filters'),
                     3 => get_string('startswith', 'filters'),
                     4 => get_string('endswith', 'filters'),
                     5 => get_string('isempty', 'filters'),
                     6 => get_string('isnotdefined', 'filters'),
                     7 => get_string('isdefined', 'filters'));
    }

    /**
     * Returns an array of custom profile fields
     * @return array of profile fields
     */
    public function get_profile_fields() {
        global $DB;
        if (!$fields = $DB->get_records('user_info_field', null, 'shortname', 'id,shortname')) {
            return null;
        }
        $res = array(0 => get_string('anyfield', 'filters'));
        foreach ($fields as $k => $v) {
            $res[$k] = $v->shortname;
        }
        return $res;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $profilefields = $this->get_profile_fields();
        if (empty($profilefields)) {
            return;
        }
        $objs = array();
        $objs[] = $mform->createElement('select', $this->_name.'_fld', null, $profilefields);
        $objs[] = $mform->createElement('select', $this->_name.'_op', null, $this->get_operators());
        $objs[] = $mform->createElement('text', $this->_name, null);
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
    public function check_data($formdata) {
        $profilefields = $this->get_profile_fields();

        if (empty($profilefields)) {
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
    public function get_sql_filter($data) {
        global $CFG, $DB;
        static $counter = 0;
        $name = 'ex_profilefield'.$counter++;

        $profilefields = $this->get_profile_fields();
        if (empty($profilefields)) {
            return '';
        }

        $profile  = $data['profile'];
        $operator = $data['operator'];
        $value    = $data['value'];

        $params = array();
        if (!array_key_exists($profile, $profilefields)) {
            return array('', array());
        }

        $where = "";
        $op = " IN ";

        if ($operator < 5 and $value === '') {
            return '';
        }

        switch($operator) {
            case 0: // Contains.
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "%$value%";
                break;
            case 1: // Does not contain.
                $where = $DB->sql_like('data', ":$name", false, false, true);
                $params[$name] = "%$value%";
                break;
            case 2: // Equal to.
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "$value";
                break;
            case 3: // Starts with.
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "$value%";
                break;
            case 4: // Ends with.
                $where = $DB->sql_like('data', ":$name", false, false);
                $params[$name] = "%$value";
                break;
            case 5: // Empty.
                $where = "data = :$name";
                $params[$name] = "";
                break;
            case 6: // Is not defined.
                $op = " NOT IN ";
                break;
            case 7: // Is defined.
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
    public function get_label($data) {
        $operators      = $this->get_operators();
        $profilefields = $this->get_profile_fields();

        if (empty($profilefields)) {
            return '';
        }

        $profile  = $data['profile'];
        $operator = $data['operator'];
        $value    = $data['value'];

        if (!array_key_exists($profile, $profilefields)) {
            return '';
        }

        $a = new stdClass();
        $a->label    = $this->_label;
        $a->value    = $value;
        $a->profile  = $profilefields[$profile];
        $a->operator = $operators[$operator];

        switch($operator) {
            case 0: // Contains.
            case 1: // Doesn't contain.
            case 2: // Equal to.
            case 3: // Starts with.
            case 4: // Ends with.
                return get_string('profilelabel', 'filters', $a);
            case 5: // Empty.
            case 6: // Is not defined.
            case 7: // Is defined.
                return get_string('profilelabelnovalue', 'filters', $a);
        }
        return '';
    }
}
