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

use core_reportbuilder\local\helpers\database;

/**
 * Number report filter
 *
 * This filter accepts a number value to perform filtering on (note that the value will be cast to float prior to comparison)
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class number extends base {

    /** @var int Any value */
    public const ANY_VALUE = 0;

    /** @var int Is not empty */
    public const IS_NOT_EMPTY = 1;

    /** @var int Is empty */
    public const IS_EMPTY = 2;

    /** @var int Less than */
    public const LESS_THAN = 3;

    /** @var int Greater than */
    public const GREATER_THAN = 4;

    /** @var int Equal to */
    public const EQUAL_TO = 5;

    /** @var int Equal or less than */
    public const EQUAL_OR_LESS_THAN = 6;

    /** @var int Equal or greater than */
    public const EQUAL_OR_GREATER_THAN = 7;

    /** @var int Range */
    public const RANGE = 8;

    /**
     * Returns an array of comparison operators
     *
     * @return array of comparison operators
     */
    private function get_operators(): array {
        $operators = [
            self::ANY_VALUE => get_string('filterisanyvalue', 'core_reportbuilder'),
            self::IS_NOT_EMPTY => get_string('filterisnotempty', 'core_reportbuilder'),
            self::IS_EMPTY => get_string('filterisempty', 'core_reportbuilder'),
            self::LESS_THAN => get_string('filterlessthan', 'core_reportbuilder'),
            self::GREATER_THAN => get_string('filtergreaterthan', 'core_reportbuilder'),
            self::EQUAL_TO => get_string('filterisequalto', 'core_reportbuilder'),
            self::EQUAL_OR_LESS_THAN => get_string('filterequalorlessthan', 'core_reportbuilder'),
            self::EQUAL_OR_GREATER_THAN => get_string('filterequalorgreaterthan', 'core_reportbuilder'),
            self::RANGE => get_string('filterrange', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Adds controls specific to this filter in the form.
     *
     * @param \MoodleQuickForm $mform
     */
    public function setup_form(\MoodleQuickForm $mform): void {
        $objs = [];

        $objs['select'] = $mform->createElement('select', $this->name . '_operator',
            get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header()), $this->get_operators());
        $mform->setType($this->name . '_operator', PARAM_INT);

        $objs['text'] = $mform->createElement('text', $this->name . '_value1',
            get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header()), ['size' => 3]);
        $mform->setType($this->name . '_value1', PARAM_LOCALISEDFLOAT);
        $mform->setDefault($this->name . '_value1', 0);
        $mform->hideIf($this->name . '_value1', $this->name . '_operator', 'in',
            [self::ANY_VALUE,  self::IS_NOT_EMPTY,  self::IS_EMPTY]);

        $objs['text2'] = $mform->createElement('text', $this->name . '_value2',
            get_string('filterfieldto', 'core_reportbuilder', $this->get_header()), ['size' => 3]);
        $mform->setType($this->name . '_value2', PARAM_LOCALISEDFLOAT);
        $mform->setDefault($this->name . '_value2', 0);
        $mform->hideIf($this->name . '_value2', $this->name . '_operator', 'noteq', self::RANGE);

        $mform->addGroup($objs, $this->name . '_grp', $this->get_header(), '', false)
            ->setHiddenLabel(true);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array array of two elements - SQL query and named parameters
     */
    public function get_sql_filter(array $values): array {
        global $DB;

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);

        $value1 = $value2 = null;
        if (array_key_exists("{$this->name}_value1", $values)) {
            $value1 = unformat_float($values["{$this->name}_value1"]);
        }
        if (array_key_exists("{$this->name}_value2", $values)) {
            $value2 = unformat_float($values["{$this->name}_value2"]);
        }

        // Validate filter form values.
        if (!$this->validate_filter_values($operator, $value1, $value2)) {
            // Filter configuration is invalid. Ignore the filter.
            return ['', []];
        }

        [$param, $param2] = database::generate_param_names(2);

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        switch ($operator) {
            case self::ANY_VALUE:
                return ['', []];
            case self::IS_NOT_EMPTY:
                $res = "COALESCE({$fieldsql}, 0) <> 0";
                break;
            case self::IS_EMPTY:
                $res = "COALESCE({$fieldsql}, 0) = 0";
                break;
            case self::LESS_THAN:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " < :{$param}";
                $params[$param] = $value1;
                break;
            case self::GREATER_THAN:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " > :{$param}";
                $params[$param] = $value1;
                break;
            case self::EQUAL_TO:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " = :{$param}";
                $params[$param] = $value1;
                break;
            case self::EQUAL_OR_LESS_THAN:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " <= :{$param}";
                $params[$param] = $value1;
                break;
            case self::EQUAL_OR_GREATER_THAN:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " >= :{$param}";
                $params[$param] = $value1;
                break;
            case self::RANGE:
                $res = $DB->sql_cast_char2real("({$fieldsql})") . " BETWEEN :{$param} AND :{$param2}";
                $params[$param] = $value1;
                $params[$param2] = $value2;
                break;
            default:
                // Filter configuration is invalid. Ignore the filter.
                return ['', []];
        }
        return [$res, $params];
    }

    /**
     * Validate filter form values
     *
     * @param int $operator
     * @param float|null $value1
     * @param float|null $value2
     * @return bool
     */
    private function validate_filter_values(int $operator, ?float $value1, ?float $value2): bool {
        // Check that for any of these operators value1 can not be null.
        $requirescomparisonvalue = [
            self::LESS_THAN,
            self::GREATER_THAN,
            self::EQUAL_TO,
            self::EQUAL_OR_LESS_THAN,
            self::EQUAL_OR_GREATER_THAN
        ];
        if (in_array($operator, $requirescomparisonvalue) && $value1 === null) {
            return false;
        }

        // When operator is between $value1 and $value2, can not be null.
        if (($operator === self::RANGE) && ($value1 === null || $value2 === null)) {
            return false;
        }

        return true;
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_operator" => self::GREATER_THAN,
            "{$this->name}_value1" => 1,
        ];
    }
}
