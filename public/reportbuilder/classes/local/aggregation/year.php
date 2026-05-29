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

use core\lang_string;
use core_reportbuilder\local\helpers\format;

/**
 * Column year aggregation type
 *
 * @package     core_reportbuilder
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class year extends datebase {
    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationyear', 'core_reportbuilder');
    }

    /**
     * Return the aggregated field SQL
     *
     * Uses DB-specific date functions to truncate timestamps to the start of the year, since years have
     * variable lengths (leap years) and cannot be handled with simple integer arithmetic
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        return static::get_date_field_sql($field, 'year');
    }

    /**
     * Return formatted value for column when applying aggregation
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks
     * @param int $columntype
     * @return string
     */
    public function format_value($value, array $values, array $callbacks, int $columntype): string {
        return format::userdate($value, (object) [], get_string('strftimeyear', 'core_langconfig'));
    }
}
