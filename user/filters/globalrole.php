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
 * Global role filter
 *
 * @package   core_user
 * @category  user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * User filter based on global roles.
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_globalrole extends user_filter_type {

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     */
    public function __construct($name, $label, $advanced) {
        parent::__construct($name, $label, $advanced);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function user_filter_globalrole($name, $label, $advanced) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($name, $label, $advanced);
    }

    /**
     * Returns an array of available roles
     * @return array of availble roles
     */
    public function get_roles() {
        $context = context_system::instance();
        $roles = array(0 => get_string('anyrole', 'filters')) + get_assignable_roles($context);
        return $roles;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $obj =& $mform->addElement('select', $this->_name, $this->_label, $this->get_roles());
        $mform->setDefault($this->_name, 0);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name);
        }
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field = $this->_name;

        if (property_exists($formdata, $field) and !empty($formdata->$field)) {
            return array('value' => (int)$formdata->$field);
        }
        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        global $CFG;
        $value = (int)$data['value'];

        $timenow = round(time(), 100);

        $sql = "id IN (SELECT userid
                         FROM {role_assignments} a
                        WHERE a.contextid=".SYSCONTEXTID." AND a.roleid=$value)";
        return array($sql, array());
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        global $DB;

        $role = $DB->get_record('role', array('id' => $data['value']));

        $a = new stdClass();
        $a->label = $this->_label;
        $a->value = '"'.role_get_name($role).'"';

        return get_string('globalrolelabel', 'filters', $a);
    }
}
