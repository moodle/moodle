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
 * Yes/No (boolean) filter.
 *
 * @package   core_user
 * @category  user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generic yes/no filter with radio buttons for integer fields.
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_yesno extends user_filter_simpleselect {

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field user table filed name
     */
    public function __construct($name, $label, $advanced, $field) {
        parent::__construct($name, $label, $advanced, $field, array(0 => get_string('no'), 1 => get_string('yes')));
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function user_filter_yesno($name, $label, $advanced, $field) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($name, $label, $advanced, $field);
    }

    /**
     * Returns the condition to be used with SQL
     *
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        static $counter = 0;
        $name = 'ex_yesno'.$counter++;

        $value = $data['value'];
        $field = $this->_field;
        if ($value == '') {
            return array();
        }
        return array("$field=:$name", array($name => $value));
    }
}
