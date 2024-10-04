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

use lang_string;
use MoodleQuickForm;
use core_reportbuilder\local\helpers\database;

/**
 * Duration report filter
 *
 * This filter accepts a number of seconds to perform filtering on (note that the value will be cast to float prior to comparison)
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class duration extends base {

    /** @var int Any value */
    public const DURATION_ANY = 0;

    /** @var int Maximum duration */
    public const DURATION_MAXIMUM = 1;

    /** @var int Minimum duration */
    public const DURATION_MINIMUM = 2;


    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::DURATION_ANY => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::DURATION_MAXIMUM => new lang_string('filterlessthan', 'core_reportbuilder'),
            self::DURATION_MINIMUM => new lang_string('filtergreaterthan', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $elements = [];

        // Operator.
        $operatorlabel = get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header());

        $elements[] = $mform->createElement('select', "{$this->name}_operator", $operatorlabel, $this->get_operators());
        $mform->setType("{$this->name}_operator", PARAM_INT);
        $mform->setDefault("{$this->name}_operator", self::DURATION_ANY);

        // Value.
        $valuelabel = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());

        $elements[] = $mform->createElement('text', "{$this->name}_value", $valuelabel, ['size' => 3]);
        $mform->setType("{$this->name}_value", PARAM_LOCALISEDFLOAT);
        $mform->setDefault("{$this->name}_value", 0);
        $mform->hideIf("{$this->name}_value", "{$this->name}_operator", 'eq', self::DURATION_ANY);

        // Unit.
        $unitlabel = get_string('filterfieldunit', 'core_reportbuilder', $this->get_header());
        $units = [
            1 => get_string('filterdateseconds', 'core_reportbuilder'),
            MINSECS => get_string('filterdateminutes', 'core_reportbuilder'),
            HOURSECS => get_string('filterdatehours', 'core_reportbuilder'),
            DAYSECS => get_string('filterdatedays', 'core_reportbuilder'),
            WEEKSECS => get_string('filterdateweeks', 'core_reportbuilder'),
        ];

        $elements[] = $mform->createElement('select', "{$this->name}_unit", $unitlabel, $units);
        $mform->setType("{$this->name}_unit", PARAM_INT);
        $mform->setDefault("{$this->name}_unit", 1);
        $mform->hideIf("{$this->name}_unit", "{$this->name}_operator", 'eq', self::DURATION_ANY);

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
        global $DB;

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $operator = (int) ($values["{$this->name}_operator"] ?? self::DURATION_ANY);

        $durationvalue = unformat_float($values["{$this->name}_value"] ?? 0);
        $durationunit = (int) ($values["{$this->name}_unit"] ?? 0);

        $paramduration = database::generate_param_name();
        $params[$paramduration] = $durationvalue * $durationunit;

        switch ($operator) {
            case self::DURATION_MAXIMUM:
                $sql = $DB->sql_cast_char2real("({$fieldsql})") . " <= :{$paramduration}";
                break;
            case self::DURATION_MINIMUM:
                $sql = $DB->sql_cast_char2real("({$fieldsql})") . " >= :{$paramduration}";
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
            "{$this->name}_operator" => self::DURATION_MAXIMUM,
            "{$this->name}_value" => 2,
            "{$this->name}_unit" => MINSECS,
        ];
    }
}
