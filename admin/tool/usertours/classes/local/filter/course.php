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
 * Course filter.
 *
 * @package    tool_usertours
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends base {
    /** @var string Option to select all courses. */
    public const OPERATOR_ALL = 'all';
    /** @var string Option to select specific courses. */
    public const OPERATOR_SELECT = 'select';
    /** @var string Option to select all courses except specific courses. */
    public const OPERATOR_EXCEPT = 'except';
    /** @var string The filter operator key constant. */
    public const OPERATOR_KEY = 'course_operator';
    /** @var string The filter key constant. */
    public const FILTER_KEY = 'filter_course';

    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name(): string {
        return 'course';
    }

    /**
     * Overrides the base add form element with a course selector.
     *
     * @param \MoodleQuickForm $mform
     */
    public static function add_filter_to_form(\MoodleQuickForm &$mform) {
        // Add the operator selector.
        $operatorkey = 'filter_' . self::OPERATOR_KEY;
        $mform->addElement('select', $operatorkey, get_string($operatorkey, 'tool_usertours'), static::get_operator_options());
        $mform->setDefault($operatorkey, static::OPERATOR_ALL);
        $mform->addHelpButton($operatorkey, $operatorkey, 'tool_usertours');

        // Add the course selector.
        $key = self::FILTER_KEY;
        $options = ['multiple' => true];
        $mform->addElement("course", $key, get_string($key, 'tool_usertours'), $options);
        $mform->setDefault($key, '0');
        $mform->addHelpButton($key, $key, 'tool_usertours');
        $mform->hideIf($key, $operatorkey, 'eq', self::OPERATOR_ALL);
    }

    /**
     * Validate form data specific to the course filter.
     *
     * @param array $data The current form data.
     * @param array $files The current form files.
     * @return array Any validation errors for this filter.
     */
    public static function validate_form(array $data, array $files): array {
        $errors = [];
        $key = static::FILTER_KEY;
        $operatorkey = 'filter_' . self::OPERATOR_KEY;
        if ($data[$operatorkey] !== static::OPERATOR_ALL && empty($data[$key])) {
            $errors[$key] = get_string('filter_course_error_course_selection', 'tool_usertours');
        }

        return $errors;
    }

    /**
     * Check whether the filter matches the specified tour and/or context.
     *
     * @param   tour        $tour       The tour to check
     * @param   context     $context    The context to check
     * @return  boolean
     */
    public static function filter_matches(tour $tour, context $context): bool {
        global $COURSE;
        $values = $tour->get_filter_values(static::get_filter_name());
        $operator = $tour->get_filter_values(static::OPERATOR_KEY)[0] ?? static::OPERATOR_ALL;

        if (empty($values) || empty($values[0])) {
            return true;
        }

        if (empty($COURSE->id)) {
            return false;
        }

        return match ($operator) {
            static::OPERATOR_SELECT => in_array($COURSE->id, $values),
            static::OPERATOR_EXCEPT => !in_array($COURSE->id, $values),
            default => true,
        };
    }

    /**
     * Overrides the base prepare the filter values for the form with an integer value.
     *
     * @param   tour            $tour       The tour to prepare values from
     * @param   stdClass        $data       The data value
     * @return  stdClass
     */
    public static function prepare_filter_values_for_form(tour $tour, \stdClass $data) {
        // Prepare the operator value.
        $operatorfiltername = static::OPERATOR_KEY;
        $operatorkey = 'filter_' . $operatorfiltername;
        $operator = $tour->get_filter_values($operatorfiltername)[0] ?? static::OPERATOR_ALL;
        $data->$operatorkey = $operator;

        // Prepare the course value.
        $filtername = static::get_filter_name();
        $key = 'filter_' . $filtername;
        $values = $tour->get_filter_values($filtername) ?: 0;
        $data->$key = $data->$operatorkey === static::OPERATOR_ALL ? 0 : $values;

        return $data;
    }

    /**
     * Overrides the base save the filter values from the form to the tour.
     *
     * @param   tour            $tour       The tour to save values to
     * @param   stdClass        $data       The data submitted in the form
     */
    public static function save_filter_values_from_form(
        tour $tour,
        \stdClass $data,
    ) {
        $operatorfiltername = static::OPERATOR_KEY;
        $operatorkey = 'filter_' . $operatorfiltername;
        $tour->set_filter_values($operatorfiltername, [$data->$operatorkey]);
        $filtername = static::get_filter_name();
        if ($data->$operatorkey === static::OPERATOR_ALL) {
            $newvalue = [];
        } else {
            $key = 'filter_' . $filtername;
            $newvalue = $data->$key;
            if (empty($data->$key)) {
                $newvalue = [];
            }
        }
        $tour->set_filter_values($filtername, $newvalue);
    }

    /**
     * Retrieve the available operator options.
     *
     * @return string[] The available operator options.
     */
    public static function get_operator_options(): array {
        $operatorkey = 'filter_' . self::OPERATOR_KEY;
        return [
            static::OPERATOR_ALL => get_string($operatorkey . '_' . static::OPERATOR_ALL, 'tool_usertours'),
            static::OPERATOR_SELECT => get_string($operatorkey . '_' . static::OPERATOR_SELECT, 'tool_usertours'),
            static::OPERATOR_EXCEPT => get_string($operatorkey . '_' . static::OPERATOR_EXCEPT, 'tool_usertours'),
        ];
    }
}
