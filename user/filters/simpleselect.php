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
 * Simple value select filter.
 *
 * @package   core_user
 * @category  user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter based on a list of values.
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_simpleselect extends user_filter_type {
    /**
     * options for the list values
     * @var array
     */
    public $_options;

    /**
     * @var string
     */
    public $_field;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field user table filed name
     * @param array $options select options
     */
    public function __construct($name, $label, $advanced, $field, $options) {
        parent::__construct($name, $label, $advanced);
        $this->_field   = $field;
        $this->_options = $options;
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function user_filter_simpleselect($name, $label, $advanced, $field, $options) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($name, $label, $advanced, $field, $options);
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param moodleform $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $choices = array('' => get_string('anyvalue', 'filters')) + $this->_options;
        $mform->addElement('select', $this->_name, $this->_label, $choices);
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

        if (array_key_exists($field, $formdata) and $formdata->$field !== '') {
            return array('value' => (string)$formdata->$field);
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        static $counter = 0;
        $name = 'ex_simpleselect'.$counter++;

        $value = $data['value'];
        $params = array();
        $field = $this->_field;
        if ($value == '') {
            return '';
        }
        return array("$field=:$name", array($name => $value));
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        $value = $data['value'];

        $a = new stdClass();
        $a->label    = $this->_label;
        $a->value    = '"'.s($this->_options[$value]).'"';
        $a->operator = get_string('isequalto', 'filters');

        return get_string('selectlabel', 'filters', $a);
    }
}

