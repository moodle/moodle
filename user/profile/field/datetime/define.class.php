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
 * Define datetime fields.
 *
 * @package profilefield_datetime
 * @copyright 2010 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class profile_define_datetime extends profile_define_base {

    /**
     * Define the setting for a datetime custom field.
     *
     * @param moodleform $form the user form
     */
    public function define_form_specific($form) {
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        // Create variables to store start and end.
        list($year, $month, $day) = explode('_', date('Y_m_d'));
        $currentdate = $calendartype->convert_from_gregorian($year, $month, $day);
        $currentyear = $currentdate['year'];
        $startyear = $calendartype->get_min_year();
        $endyear = $calendartype->get_max_year();

        // Create array for the years.
        $arryears = array();
        for ($i = $startyear; $i <= $endyear; $i++) {
            $arryears[$i] = $i;
        }

        // Add elements.
        $form->addElement('select', 'param1', get_string('startyear', 'profilefield_datetime'), $arryears);
        $form->setType('param1', PARAM_INT);
        $form->setDefault('param1', $currentyear);

        $form->addElement('select', 'param2', get_string('endyear', 'profilefield_datetime'), $arryears);
        $form->setType('param2', PARAM_INT);
        $form->setDefault('param2', $currentyear);

        $form->addElement('checkbox', 'param3', get_string('wanttime', 'profilefield_datetime'));
        $form->setType('param3', PARAM_INT);

        $form->addElement('hidden', 'defaultdata', '0');
        $form->setType('defaultdata', PARAM_INT);
    }

    /**
     * Validate the data from the profile field form.
     *
     * @param stdClass $data from the add/edit profile field form
     * @param array $files
     * @return array associative array of error messages
     */
    public function define_validate_specific($data, $files) {
        $errors = array();

        // Make sure the start year is not greater than the end year.
        if ($data->param1 > $data->param2) {
            $errors['param1'] = get_string('startyearafterend', 'profilefield_datetime');
        }

        return $errors;
    }

    /**
     * Alter form based on submitted or existing data.
     *
     * @param moodleform $mform
     */
    public function define_after_data(&$mform) {
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        // The start and end year will be set as a Gregorian year in the DB. We want
        // to convert these to the equivalent year in the current calendar system.
        $param1 = $mform->getElement('param1');
        $year = $param1->getValue(); // The getValue() for select elements returns an array.
        $year = $year[0];
        $date1 = $calendartype->convert_from_gregorian($year, 1, 1);

        $param2 = $mform->getElement('param2');
        $year = $param2->getValue(); // The getValue() for select elements returns an array.
        $year = $year[0];
        $date2 = $calendartype->convert_from_gregorian($year, 1, 1);

        $param1->setValue($date1['year']);
        $param2->setValue($date2['year']);
    }

    /**
     * Preprocess data from the profile field form before
     * it is saved.
     *
     * @param stdClass $data from the add/edit profile field form
     * @return stdClass processed data object
     */
    public function define_save_preprocess($data) {
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        // Ensure the years are saved as Gregorian in the database.
        $startdate = $calendartype->convert_to_gregorian($data->param1, 1, 1);
        $stopdate = $calendartype->convert_to_gregorian($data->param2, 1, 1);

        $data->param1 = $startdate['year'];
        $data->param2 = $stopdate['year'];

        if (empty($data->param3)) {
            $data->param3 = null;
        }

        // No valid value in the default data column needed.
        $data->defaultdata = '0';

        return $data;
    }
}
