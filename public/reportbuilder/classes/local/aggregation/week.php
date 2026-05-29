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
 * Column week aggregation type
 *
 * @package     core_reportbuilder
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class week extends datebase {
    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationweek', 'core_reportbuilder');
    }

    /**
     * Return the aggregated field SQL
     *
     * The Unix epoch (Jan 1, 1970) was a Thursday. We calculate the offset needed to align week
     * boundaries to the configured start day of the week
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        $weeksecs = WEEKSECS;

        // Determine the day-of-week shift to align week boundaries. The epoch started on Thursday (day 4 in
        // the 0=Sunday system). We compute how many days from the configured start day to Thursday.
        $startday = \core_calendar\type_factory::get_calendar_instance()->get_starting_weekday();
        $epochshift = ((4 - $startday + 7) % 7) * DAYSECS;

        return "FLOOR(({$field} + {$epochshift}) / {$weeksecs}) * {$weeksecs} - {$epochshift}";
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
