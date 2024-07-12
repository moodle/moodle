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

namespace core_reportbuilder\local\aggregation;

use lang_string;
use core_reportbuilder\local\report\column;

/**
 * Column count distinct aggregation type
 *
 * The formatted value applied to aggregated columns can be customised by passing a callable as the 'callback' option
 * via {@see column::set_aggregation} or {@see column::set_aggregation_options} methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class countdistinct extends count {

    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationcountdistinct', 'core_reportbuilder');
    }

    /**
     * Override base method to ensure all SQL fields are concatenated together if there are multiple
     *
     * @param array $sqlfields
     * @return string
     */
    public static function get_column_field_sql(array $sqlfields): string {
        if (count($sqlfields) === 1) {
            return parent::get_column_field_sql($sqlfields);
        }

        return self::get_column_fields_concat($sqlfields);
    }

    /**
     * Return the aggregated field SQL
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        return "COUNT(DISTINCT {$field})";
    }
}
