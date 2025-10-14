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

namespace core_badges\reportbuilder\local\filters;

use core\lang_string;
use core_collator;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\helpers\database;

/**
 * Badge criteria filter
 *
 * Does not require a join with the badge criteria table, and each badge is returned once regardless of number of times it contains
 * (or doesn't) the matching criteria
 *
 * Filter field SQL should be the ID of a badge, e.g. "{$badgealias}.id"
 *
 * @package     core_badges
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class criteria extends select {
    /**
     * Returns an array of comparison operators
     *
     * @return array
     */
    protected function get_operators(): array {
        return [
            self::ANY_VALUE => get_string('filterisanyvalue', 'core_reportbuilder'),
            self::EQUAL_TO => get_string('filtercontains', 'core_reportbuilder'),
            self::NOT_EQUAL_TO => get_string('filterdoesnotcontain', 'core_reportbuilder'),
        ];
    }

    /**
     * Return the options for the filter as an array, to be used to populate the select input field
     *
     * @return array
     */
    protected function get_select_options(): array {
        global $CFG;
        require_once("{$CFG->libdir}/badgeslib.php");

        $options = [];
        foreach (badges_list_criteria() as $index => $criteria) {
            if ($index !== BADGE_CRITERIA_TYPE_OVERALL) {
                $options[$index] = new lang_string("criteria_{$index}", 'core_badges');
            }
        }

        core_collator::asort($options);
        return $options;
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);
        $value = (string) ($values["{$this->name}_value"] ?? '');

        // Validate filter form values.
        if ($operator === self::ANY_VALUE || empty($value)) {
            return ['', []];
        }

        $fieldsql = $this->filter->get_field_sql();
        $fieldparams = $this->filter->get_field_params();

        $criteriatablealias = database::generate_alias();
        $criteriaparam = database::generate_param_name();

        $criteriasql = "EXISTS (
            SELECT 1
              FROM {badge_criteria} {$criteriatablealias}
             WHERE {$criteriatablealias}.badgeid = {$fieldsql}
               AND {$criteriatablealias}.criteriatype = :{$criteriaparam}
        )";

        $fieldparams[$criteriaparam] = $value;

        // If specified "Not equal to", then negate the entire clause.
        if ($operator === self::NOT_EQUAL_TO) {
            $criteriasql = "NOT ({$criteriasql})";
        }

        return [$criteriasql, $fieldparams];
    }
}
