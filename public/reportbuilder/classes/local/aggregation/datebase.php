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

use core_reportbuilder\local\report\column;

/**
 * Abstract base class for date-related column aggregation types
 *
 * @package     core_reportbuilder
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class datebase extends base {
    /**
     * This aggregation can be performed on timestamp columns
     *
     * @param int $columntype
     * @return bool
     */
    public static function compatible(int $columntype): bool {
        return $columntype === column::TYPE_TIMESTAMP;
    }

    /**
     * When applied to a column, we should group by its fields
     *
     * @return bool
     */
    public static function column_groupby(): bool {
        return true;
    }

    /**
     * Returns aggregated column type
     *
     * @param int $columntype
     * @return int
     */
    public static function get_column_type(int $columntype): int {
        return column::TYPE_TIMESTAMP;
    }

    /**
     * Return SQL to truncate a timestamp field to a given date boundary using DB-specific date functions
     *
     * The database session timezone matches the server timezone, so epoch to datetime conversion already
     * produces the correct local time. No additional offset is needed. The returned epoch represents the
     * start of the local period, and should be formatted with {@see userdate} which will apply the same
     * timezone for display
     *
     * @param string $field
     * @param string $unit The truncation unit: 'month' or 'year'
     * @return string
     */
    protected static function get_date_field_sql(string $field, string $unit): string {
        global $DB;

        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily === 'postgres') {
            return "EXTRACT(EPOCH FROM DATE_TRUNC('{$unit}', TO_TIMESTAMP({$field})))";
        }

        if ($dbfamily === 'mssql') {
            // Convert epoch to datetime, truncate to the start of the unit, then convert back to epoch. We use
            // FORMAT to truncate via string formatting, ensuring {$field} is evaluated only once.
            $datetime = "DATEADD(SECOND, {$field}, '1970-01-01')";
            $formats = [
                'month' => 'yyyy-MM-01',
                'year' => 'yyyy-01-01',
            ];
            return "DATEDIFF(SECOND, '1970-01-01', CONVERT(DATETIME, FORMAT({$datetime}, '{$formats[$unit]}')))";
        }

        // MySQL/MariaDB.
        $dateformats = [
            'month' => '%Y-%m-01',
            'year' => '%Y-01-01',
        ];
        return "UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME({$field}), '{$dateformats[$unit]}'))";
    }
}
