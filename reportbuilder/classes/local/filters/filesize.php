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
use lang_string;
use MoodleQuickForm;

/**
 * Filesize report filter
 *
 * @package     core_reportbuilder
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filesize extends base {

    /** @var int Any value */
    public const ANY_VALUE = 0;

    /** @var int Less than */
    public const LESS_THAN = 3;

    /** @var int Greater than */
    public const GREATER_THAN = 4;

    /** @var int Bytes */
    public const SIZE_UNIT_BYTE = 1;

    /** @var int Kilobytes */
    public const SIZE_UNIT_KILOBYTE = 1024;

    /** @var int Megabytes */
    public const SIZE_UNIT_MEGABYTE = self::SIZE_UNIT_KILOBYTE * 1024;

    /** @var int Gigabytes */
    public const SIZE_UNIT_GIGABYTE = self::SIZE_UNIT_MEGABYTE * 1024;

    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::ANY_VALUE => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::LESS_THAN => new lang_string('filterlessthan', 'core_reportbuilder'),
            self::GREATER_THAN => new lang_string('filtergreaterthan', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        // Operator selector.
        $elements[] = $mform->createElement('select', "{$this->name}_operator",
            get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header()), $this->get_operators());
        $mform->setType("{$this->name}_operator", PARAM_INT);
        $mform->setDefault("{$this->name}_operator", self::ANY_VALUE);

        // Value selector.
        $elements[] = $mform->createElement('text', "{$this->name}_value1",
            get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header()), ['size' => 4]);
        $mform->setType("{$this->name}_value1", PARAM_FLOAT);
        $mform->setDefault("{$this->name}_value1", 1);
        $mform->hideIf("{$this->name}_value1", "{$this->name}_operator", 'eq', self::ANY_VALUE);

        // Unit selector.
        $units = [
            self::SIZE_UNIT_BYTE => new lang_string('sizeb'),
            self::SIZE_UNIT_KILOBYTE => new lang_string('sizekb'),
            self::SIZE_UNIT_MEGABYTE => new lang_string('sizemb'),
            self::SIZE_UNIT_GIGABYTE => new lang_string('sizegb'),
        ];

        $elements[] = $mform->createElement('select', "{$this->name}_unit",
            get_string('filterfieldunit', 'core_reportbuilder', $this->get_header()), $units);
        $mform->setType("{$this->name}_unit", PARAM_INT);
        $mform->setDefault("{$this->name}_unit", self::SIZE_UNIT_BYTE);
        $mform->hideIf("{$this->name}_unit", "{$this->name}_operator", 'eq', self::ANY_VALUE);

        $mform->addGroup($elements, "{$this->name}_group", $this->get_header(), '', false)
            ->setHiddenLabel(true);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);

        $filesizevalue = unformat_float($values["{$this->name}_value1"] ?? 1);
        $filesizeunit = (int) ($values["{$this->name}_unit"] ?? self::SIZE_UNIT_BYTE);

        $paramfilesize = database::generate_param_name();
        $params[$paramfilesize] = $filesizevalue * $filesizeunit;

        switch ($operator) {
            case self::LESS_THAN:
                $sql = "{$fieldsql} < :{$paramfilesize}";
                break;
            case self::GREATER_THAN:
                $sql = "{$fieldsql} > :{$paramfilesize}";
                break;
            default:
                // Invalid or inactive filter.
                return ['', []];
        }

        return [$sql, $params];
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
            "{$this->name}_unit" => self::SIZE_UNIT_KILOBYTE,
        ];
    }
}
