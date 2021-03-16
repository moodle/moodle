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
 * Date report filter
 *
 * This filter accepts a unix timestamp to perform date filtering on
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date extends base {

    /** @var int Any value */
    public const DATE_ANY = 0;

    /** @var int Non-empty (positive) value */
    public const DATE_NOT_EMPTY = 1;

    /** @var int Empty (zero) value */
    public const DATE_EMPTY = 2;

    /** @var int Date within defined range */
    public const DATE_RANGE = 3;

    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::DATE_ANY => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::DATE_NOT_EMPTY => new lang_string('filterisnotempty', 'core_reportbuilder'),
            self::DATE_EMPTY => new lang_string('filterisempty', 'core_reportbuilder'),
            self::DATE_RANGE => new lang_string('filterrange', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $operatorlabel = get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header());
        $mform->addElement('select', "{$this->name}_operator", $operatorlabel, $this->get_operators())->setHiddenLabel(true);
        $mform->setType("{$this->name}_operator", PARAM_INT);
        $mform->setDefault("{$this->name}_operator", self::DATE_ANY);

        $mform->addElement('date_selector', "{$this->name}_from", get_string('filterdatefrom', 'core_reportbuilder'),
            ['optional' => true]);
        $mform->setType("{$this->name}_from", PARAM_INT);
        $mform->setDefault("{$this->name}_from", 0);
        $mform->hideIf("{$this->name}_from", "{$this->name}_operator", 'neq', self::DATE_RANGE);

        $mform->addElement('date_selector', "{$this->name}_to", get_string('filterdateto', 'core_reportbuilder'),
            ['optional' => true]);
        $mform->setType("{$this->name}_to", PARAM_INT);
        $mform->setDefault("{$this->name}_to", 0);
        $mform->hideIf("{$this->name}_to", "{$this->name}_operator", 'neq', self::DATE_RANGE);
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

        $operator = $values["{$this->name}_operator"] ?? self::DATE_ANY;
        switch ($operator) {
            case self::DATE_NOT_EMPTY:
                $sql = "{$fieldsql} IS NOT NULL AND {$fieldsql} <> 0";
                break;
            case self::DATE_EMPTY:
                $sql = "{$fieldsql} IS NULL OR {$fieldsql} = 0";
                break;
            case self::DATE_RANGE:
                $clauses = [];

                $datefrom = (int)($values["{$this->name}_from"] ?? 0);
                if ($datefrom > 0) {
                    $paramdatefrom = database::generate_param_name();
                    $clauses[] = "{$fieldsql} >= :{$paramdatefrom}";
                    $params[$paramdatefrom] = $datefrom;
                }

                $dateto = (int)($values["{$this->name}_to"] ?? 0);
                if ($dateto > 0) {
                    $paramdateto = database::generate_param_name();
                    $clauses[] = "{$fieldsql} < :{$paramdateto}";
                    $params[$paramdateto] = $dateto;
                }

                $sql = implode(' AND ', $clauses);

                break;
            default:
                // Invalid or inactive filter.
                return ['', []];
        }

        return [$sql, $params];
    }
}
