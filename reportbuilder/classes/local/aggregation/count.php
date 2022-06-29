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
 * Column count aggregation type
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class count extends base {

    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationcount', 'core_reportbuilder');
    }

    /**
     * This aggregation can be performed on all column types
     *
     * @param int $columntype
     * @return bool
     */
    public static function compatible(int $columntype): bool {
        return true;
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

        if ($columntype === column::TYPE_LONGTEXT && $DB->get_dbfamily() === 'oracle') {
            $field = $DB->sql_compare_text($field, 255);
        }

        return "COUNT({$field})";
    }

    /**
     * Return formatted value for column when applying aggregation
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks
     * @return mixed
     */
    public static function format_value($value, array $values, array $callbacks) {
        return (int) reset($values);
    }
}
