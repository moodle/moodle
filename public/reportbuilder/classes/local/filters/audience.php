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

use core_reportbuilder\local\helpers\audience as audience_helper;
use core_reportbuilder\local\models\audience as audience_model;

/**
 * Report audience filter
 *
 * Specific to the report access list, to allow for filtering of the user list according to the audience they belong to
 *
 * In order to specify for which report we are viewing the access list for, the following options must be passed
 * to the filter {@see \core_reportbuilder\local\report\filter::set_options} method
 *
 * ['reportid' => '...']
 *
 * @package     core_reportbuilder
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience extends select {

    /**
     * Return the options for the filter as an array, to be used to populate the select input field
     *
     * @return array
     */
    protected function get_select_options(): array {
        $options = [];

        $audiences = audience_helper::get_base_records($this->filter->get_options()['reportid'] ?? 0);
        foreach ($audiences as $audience) {
            $persistent = $audience->get_persistent();

            // Check for a custom name, otherwise fall back to default.
            if ('' === $audiencelabel = $persistent->get_formatted_heading()) {
                $audiencelabel = $audience->get_name();
            }

            $options[$persistent->get('id')] = $audiencelabel;
        }

        return $options;
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        $reportid = $this->filter->get_options()['reportid'] ?? 0;

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);
        $audienceid = (int) ($values["{$this->name}_value"] ?? 0);

        switch ($operator) {
            case self::EQUAL_TO:
            case self::NOT_EQUAL_TO:
                $audience = audience_model::get_record(['id' => $audienceid, 'reportid' => $reportid]);
                if ($audience === false) {
                    return ['', []];
                }

                // Generate audience SQL, invert it for "not equal to".
                [$select, $params] = audience_helper::user_audience_single_sql($audience, $this->filter->get_field_sql());
                if ($operator === self::NOT_EQUAL_TO) {
                    $select = "NOT {$select}";
                }

                break;
            default:
                return ['', []];
        }

        return [$select, $params];
    }
}
