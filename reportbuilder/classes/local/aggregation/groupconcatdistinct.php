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
     * This aggregation can be performed on all non-timestamp columns in MySQL and Postgres only
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

        // DB limitations mean we only support MySQL and Postgres, and each handle it differently.
        $fieldsort = database::sql_group_concat_sort($field);
        if ($DB->get_dbfamily() === 'postgres') {
            if ($fieldsort !== '') {
                $fieldsort = "ORDER BY {$fieldsort}";
            }

            return "STRING_AGG(DISTINCT CAST({$field} AS VARCHAR), '" . self::FIELD_VALUE_DELIMETER . "' {$fieldsort})";
        } else {
            return $DB->sql_group_concat("DISTINCT {$field}", self::FIELD_VALUE_DELIMETER, $fieldsort);
        }
    }
}
