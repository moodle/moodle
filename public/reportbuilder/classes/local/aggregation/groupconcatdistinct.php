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
use core_reportbuilder\local\helpers\database;

/**
 * Column group concatenation distinct aggregation type
 *
 * The value used for the separator between aggregated items can be specified by passing the 'separator' option
 * via {@see column::set_aggregation} or {@see column::set_aggregation_options} methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupconcatdistinct extends groupconcat {

    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationgroupconcatdistinct', 'core_reportbuilder');
    }

    /**
     * This aggregation can be performed on all non-timestamp columns in supported DBs
     *
     * @param int $columntype
     * @return bool
     */
    public static function compatible(int $columntype): bool {
        global $DB;

        $dbsupportedtype = in_array($DB->get_dbfamily(), [
            'mysql',
            'postgres',
        ]);

        return $dbsupportedtype && parent::compatible($columntype);
    }

    /**
     * Return the aggregated field SQL
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        global $DB;

        $fieldsort = database::sql_group_concat_sort($field);

        // Postgres handles group concatenation differently in that it requires the expression to be cast to char, so we can't
        // simply pass "DISTINCT {$field}" to the {@see \moodle_database::sql_group_concat} method in all cases.
        if ($DB->get_dbfamily() === 'postgres') {
            $field = $DB->sql_cast_to_char($field);
            if ($fieldsort !== '') {
                $fieldsort = "ORDER BY {$fieldsort}";
            }

            return "STRING_AGG(DISTINCT {$field}, '" . self::FIELD_VALUE_DELIMETER . "' {$fieldsort})";
        } else {
            return $DB->sql_group_concat("DISTINCT {$field}", self::FIELD_VALUE_DELIMETER, $fieldsort);
        }
    }
}
