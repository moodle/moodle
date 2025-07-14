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

use core\{clock, di};
use core\lang_string;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;

/**
 * Column date aggregation type
 *
 * @package     core_reportbuilder
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date extends base {

    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationdate', 'core_reportbuilder');
    }

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
     * Return the aggregated field SQL
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        $datenow = di::get(clock::class)->now();

        // Apply timezone offset for current user.
        return "(FLOOR({$field} / " . DAYSECS . ") * " . DAYSECS . ") + " . $datenow->getOffset();
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
     * Return formatted value for column when applying aggregation
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks
     * @param int $columntype
     * @return string
     */
    public function format_value($value, array $values, array $callbacks, int $columntype): string {
        return format::userdate($value, (object) [], get_string('strftimedaydate', 'core_langconfig'));
    }
}
