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
 * Generic checkbox filter.
 *
 * This will create generic filter with checkbox option and can be used for
 * disabling other elements for specific condition.
 *
 * @package    user
 * @copyright  2011 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * Generic filter based for checkbox and can be used for disabling items
 */
class user_filter_checkbox extends user_filter_type {
    /**
     * list of all the fields which needs to be disabled, if checkbox is checked
     * @var array
     */
    protected $disableelements = array();

    /**
     * name of user table field/fields on which data needs to be compared
     * @var mixed
     */
    protected $field;

    /**
     * Constructor, initalize user_filter_type and sets $disableelements array
     * with list of elements to be diabled by checkbox.
     *
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param mixed $field user table field/fields name for comparison
     * @param array $disableelements name of fields which should be disabled if this checkbox is checked.
     */
    public function __construct($name, $label, $advanced, $field, $disableelements=null) {
        parent::__construct($name, $label, $advanced);
        $this->field   = $field;
        if (!empty($disableelements)) {
            if (!is_array($disableelements)) {
                $this->disableelements = array($disableelements);
            } else {
                $this->disableelements = $disableelements;
            }
        }
    }

    /**
     * Adds controls specific to this filter in the form.
     *
     * @param moodleform $mform a MoodleQuickForm object in which element will be added
     */
    public function setupForm(MoodleQuickForm &$mform) {
        $objs = array();

        $objs[] = $mform->createElement('checkbox', $this->_name, null, '');
        $grp = $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);

        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
        //Check if disable if options are set. if yes then set rules
        if (!empty($this->disableelements) && is_array($this->disableelements)) {
            foreach ($this->disableelements as $disableelement) {
                $mform->disabledIf($disableelement, $this->_name, 'checked');
            }
        }
    }

    /**
     * Retrieves data from the form data
     *
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field = $this->_name;
        //Check if disable if options are set. if yes then don't add this.
        if (!empty($this->disableelements) && is_array($this->disableelements)) {
            foreach ($this->disableelements as $disableelement) {
                if (array_key_exists($disableelement, $formdata)) {
                    return false;
                }
            }
        }
        if (array_key_exists($field, $formdata) and $formdata->$field !== '') {
            return array('value' => (string)$formdata->$field);
        }
        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     *
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        $field  = $this->field;
        if (is_array($field)) {
            $res = " {$field[0]} = {$field[1]} ";
        } else {
            $res = " {$field} = 0 ";
        }
        return array($res, array());
    }

    /**
     * Returns a human friendly description of the filter used as label.
     *
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        return $this->_label;
    }
}
