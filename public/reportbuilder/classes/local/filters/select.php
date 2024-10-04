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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use MoodleQuickForm;
use core_reportbuilder\local\helpers\database;

/**
 * Select report filter
 *
 * The options for the select are defined when creating the filter by calling {@see set_options} or {@see set_options_callback}
 *
 * To extend this class in your own filter (e.g. to pre-populate available options), you should override the {@see get_operators}
 * and/or {@see get_select_options} methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select extends base {

    /** @var int Any value */
    public const ANY_VALUE = 0;

    /** @var int Equal to */
    public const EQUAL_TO = 1;

    /** @var int Not equal to */
    public const NOT_EQUAL_TO = 2;

    /** @var int Value to indicate "Any value" for the simplified filter options  */
    private const OPTION_ANY_VALUE = -124567;

    /**
     * Returns an array of comparison operators
     *
     * @return array
     */
    protected function get_operators(): array {
        $operators = [
            self::ANY_VALUE => get_string('filterisanyvalue', 'core_reportbuilder'),
            self::EQUAL_TO => get_string('filterisequalto', 'core_reportbuilder'),
            self::NOT_EQUAL_TO => get_string('filterisnotequalto', 'core_reportbuilder')
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Return the options for the filter as an array, to be used to populate the select input field
     *
     * @return array
     */
    protected function get_select_options(): array {
        static $options = [];

        if (!array_key_exists($this->name, $options)) {
            $options[$this->name] = (array) $this->filter->get_options();
        }

        return $options[$this->name];
    }

    /**
     * Adds controls specific to this filter in the form.
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $operators = $this->get_operators();
        $options = $this->get_select_options();

        // If a multidimensional array is passed, we need to use a different element type.
        $optioncountrecursive = count($options, COUNT_RECURSIVE);
        $element = (count($options) === $optioncountrecursive ? 'select' : 'selectgroups');

        // If operators are unrestricted, and we have upto two options, then simplify the filter to list only those.
        if (count($operators) === 3 && $optioncountrecursive <= 2) {
            $mform->addElement('hidden', "{$this->name}_operator");
            $mform->setType("{$this->name}_operator", PARAM_INT);
            $mform->setConstant("{$this->name}_operator", self::EQUAL_TO);

            $mform->addElement(
                $element,
                "{$this->name}_value",
                get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header()),
                [self::OPTION_ANY_VALUE => $operators[self::ANY_VALUE]] + $options,
            )->setHiddenLabel(true);
        } else {
            $elements = [];

            $elements[] = $mform->createElement(
                'select',
                "{$this->name}_operator",
                get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header()),
                $operators,
            );

            $elements[] = $mform->createElement(
                $element,
                "{$this->name}_value",
                get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header()),
                $options,
            );

            $mform->addGroup($elements, "{$this->name}_group", $this->get_header(), '', false)
                ->setHiddenLabel(true);

            $mform->hideIf("{$this->name}_value", "{$this->name}_operator", 'eq', self::ANY_VALUE);
        }
    }

    /**
     * Return filter SQL
     *
     * Note that operators must be of type integer, while values can be integer or string.
     *
     * @param array $values
     * @return array array of two elements - SQL query and named parameters
     */
    public function get_sql_filter(array $values): array {
        $name = database::generate_param_name();

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);
        $value = (string) ($values["{$this->name}_value"] ?? self::OPTION_ANY_VALUE);

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        // Get available options, if multidimensional then flatten the array.
        $options = $this->get_select_options();
        if (count($options) !== count($options, COUNT_RECURSIVE)) {
            $options = array_merge(...array_values($options));
        }

        // Validate filter form values.
        if ($operator === self::ANY_VALUE || !array_key_exists($value, $options)) {
            return ['', []];
        }

        switch ($operator) {
            case self::EQUAL_TO:
                $fieldsql .= "=:$name";
                $params[$name] = $value;
                break;
            case self::NOT_EQUAL_TO:
                $fieldsql .= "<>:$name";
                $params[$name] = $value;
                break;
            default:
                return ['', []];
        }
        return [$fieldsql, $params];
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_operator" => self::EQUAL_TO,
            "{$this->name}_value" => 1,
        ];
    }
}
