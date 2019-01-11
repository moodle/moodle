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
 * Customfield date plugin
 *
 * @package   customfield_date
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_date;

use core_customfield\api;

defined('MOODLE_INTERNAL') || die;

/**
 * Class data
 *
 * @package customfield_date
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the name of the field where the information is stored
     * @return string
     */
    public function datafield() : string {
        return 'intvalue';
    }

    /**
     * Add fields for editing data of a date field on a context.
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition(\MoodleQuickForm $mform) {
        $field = $this->get_field();
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $config = $field->get('configdata');

        // Always set the form element to "optional", even when it's required. Otherwise it defaults to the
        // current date and is easy to miss.
        $attributes = ['optional' => true];

        if (!empty($config['mindate'])) {
            $attributes['startyear'] = $calendartype->timestamp_to_date_array($config['mindate'])['year'];
        }

        if (!empty($config['maxdate'])) {
            $attributes['stopyear'] = $calendartype->timestamp_to_date_array($config['maxdate'])['year'];
        }

        if (empty($config['includetime'])) {
            $element = 'date_selector';
        } else {
            $element = 'date_time_selector';
        }
        $elementname = $this->get_form_element_name();
        $mform->addElement($element, $elementname, $this->get_field()->get_formatted_name(), $attributes);
        $mform->setType($elementname, PARAM_INT);
        $mform->setDefault($elementname, time());
        if ($field->get_configdata_property('required')) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }
    }

    /**
     * Validates data for this field.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function instance_form_validation(array $data, array $files) : array {
        $errors = parent::instance_form_validation($data, $files);

        $elementname = $this->get_form_element_name();
        if (!empty($data[$elementname])) {
            // Compare the date with min/max values, trim the date to the minute or to the day (depending on inludetime setting).
            $includetime = $this->get_field()->get_configdata_property('includetime');
            $machineformat = $includetime ? '%Y-%m-%d %H:%M' : '%Y-%m-%d';
            $humanformat = $includetime ? get_string('strftimedatetimeshort') : get_string('strftimedatefullshort');
            $value = userdate($data[$elementname], $machineformat, 99, false, false);
            $mindate = $this->get_field()->get_configdata_property('mindate');
            $maxdate = $this->get_field()->get_configdata_property('maxdate');

            if ($mindate && userdate($mindate, $machineformat, 99, false, false) > $value) {
                $errors[$elementname] = get_string('errormindate', 'customfield_date', userdate($mindate, $humanformat));
            }
            if ($maxdate && userdate($maxdate, $machineformat, 99, false, false) < $value) {
                $errors[$elementname] = get_string('errormaxdate', 'customfield_date', userdate($maxdate, $humanformat));
            }
        }

        return $errors;
    }

    /**
     * Returns the default value as it would be stored in the database (not in human-readable format).
     *
     * @return mixed
     */
    public function get_default_value() {
        return 0;
    }

    /**
     * Returns value in a human-readable format
     *
     * @return mixed|null value or null if empty
     */
    public function export_value() {
        $value = $this->get_value();

        if ($this->is_empty($value)) {
            return null;
        }

        // Check if time needs to be included.
        if ($this->get_field()->get_configdata_property('includetime')) {
            $format = get_string('strftimedaydatetime', 'langconfig');
        } else {
            $format = get_string('strftimedate', 'langconfig');
        }

        return userdate($value, $format);
    }
}
