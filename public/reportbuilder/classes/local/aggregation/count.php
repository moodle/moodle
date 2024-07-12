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
 * The formatted value applied to aggregated columns can be customised by passing a callable as the 'callback' option
 * via {@see column::set_aggregation} or {@see column::set_aggregation_options} methods
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
        return "COUNT({$field})";
    }

    /**
     * Returns aggregated column type
     *
     * @param int $columntype
     * @return int
     */
    public static function get_column_type(int $columntype): int {
        return column::TYPE_INTEGER;
    }

    /**
     * Return formatted value for column when applying aggregation
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks
     * @param int $columntype
     * @return mixed
     */
    public function format_value($value, array $values, array $callbacks, int $columntype) {
        $count = (int) reset($values);

        // Determine callback based on passed options.
        if (array_key_exists('callback', $this->options) && is_callable($this->options['callback'])) {
            return ($this->options['callback'])($count);
        }

        return $count;
    }
}
