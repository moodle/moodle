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

namespace tool_usertours\local\clientside_filter;

use stdClass;
use tool_usertours\tour;

/**
 * Course filter.
 *
 * @package    tool_usertours
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cssselector extends clientside_filter {
    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name() {
        return 'cssselector';
    }

    /**
     * Overrides the base add form element with a selector text box.
     *
     * @param \MoodleQuickForm $mform
     */
    public static function add_filter_to_form(\MoodleQuickForm &$mform) {
        $filtername = self::get_filter_name();
        $key = "filter_{$filtername}";

        $mform->addElement('text', $key, get_string($key, 'tool_usertours'), ['size' => '80']);
        $mform->setType($key, PARAM_RAW);
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
            $values = [""];
        }
        $data->$key = $values[0];

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

        $newvalue = [$data->$key];
        if (empty($data->$key)) {
            $newvalue = [];
        }

        $tour->set_filter_values($filtername, $newvalue);
    }

    /**
     * Returns the filter values needed for client side filtering.
     *
     * @param   tour            $tour       The tour to find the filter values for
     * @return  stdClass
     */
    public static function get_client_side_values(tour $tour): stdClass {
        $filtername = static::get_filter_name();
        $filtervalues = $tour->get_filter_values($filtername);

        // Filter values might not exist for tours that were created before this filter existed.
        if (!$filtervalues) {
            return new stdClass();
        }

        return (object) $filtervalues;
    }
}
