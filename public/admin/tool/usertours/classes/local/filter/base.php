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

namespace tool_usertours\local\filter;

use tool_usertours\tour;
use context;

/**
 * Filter base.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {
    /**
     * Any Value.
     */
    const ANYVALUE = '__ANYVALUE__';

    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name() {
        throw new \coding_exception('get_filter_name() must be defined');
    }

    /**
     * Retrieve the list of available filter options.
     *
     * @return  array                   An array whose keys are the valid options
     */
    public static function get_filter_options() {
        return [];
    }

    /**
     * Check whether the filter matches the specified tour and/or context.
     *
     * @param   tour        $tour       The tour to check
     * @param   context     $context    The context to check
     * @return  boolean
     */
    public static function filter_matches(tour $tour, context $context) {
        return true;
    }

    /**
     * Add the form elements for the filter to the supplied form.
     *
     * @param   MoodleQuickForm $mform      The form to add filter settings to.
     */
    public static function add_filter_to_form(\MoodleQuickForm &$mform) {
        $options = [
            static::ANYVALUE   => get_string('all'),
        ];
        $options += static::get_filter_options();

        $filtername = static::get_filter_name();
        $key = "filter_{$filtername}";

        $mform->addElement('select', $key, get_string($key, 'tool_usertours'), $options, [
                'multiple' => true,
            ]);
        $mform->setDefault($key, static::ANYVALUE);
        $mform->addHelpButton($key, $key, 'tool_usertours');
    }

    /**
     * Prepare the filter values for the form.
     *
     * @param   tour            $tour       The tour to prepare values from
     * @param   stdClass        $data       The data value
     * @return  stdClass
     */
    public static function prepare_filter_values_for_form(tour $tour, \stdClass $data) {
        $filtername = static::get_filter_name();

        $key = "filter_{$filtername}";
        $values = $tour->get_filter_values($filtername);
        if (empty($values)) {
            $values = static::ANYVALUE;
        }
        $data->$key = $values;

        return $data;
    }

    /**
     * Save the filter values from the form to the tour.
     *
     * @param   tour            $tour       The tour to save values to
     * @param   stdClass        $data       The data submitted in the form
     */
    public static function save_filter_values_from_form(tour $tour, \stdClass $data) {
        $filtername = static::get_filter_name();

        $key = "filter_{$filtername}";

        $newvalue = $data->$key;
        foreach ($data->$key as $value) {
            if ($value === static::ANYVALUE) {
                $newvalue = [];
                break;
            }
        }

        $tour->set_filter_values($filtername, $newvalue);
    }

    /**
     * Default validation for filter forms.
     * Returns an empty array by default if not overridden.
     *
     * @param array $data  The submitted form data.
     * @param array $files The files submitted with the form.
     * @return array       The errors array.
     */
    public static function validate_form(array $data, array $files): array {
        // Default implementation, returns no errors.
        return [];
    }
}
