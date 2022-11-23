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

namespace core_reportbuilder\local\helpers;

use stdClass;

/**
 * Class containing helper methods for formatting column data via callbacks
 *
 * @package     core_reportbuilder
 * @copyright   2021 Sara Arjona <sara@moodle.com> based on Alberto Lara Hernández <albertolara@moodle.com> code.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format {

    /**
     * Returns formatted date.
     *
     * @param int|null $value Unix timestamp
     * @param stdClass $row
     * @param string|null $format Format string for strftime
     * @return string
     */
    public static function userdate(?int $value, stdClass $row, ?string $format = null): string {
        return $value ? userdate($value, $format) : '';
    }

    /**
     * Returns yes/no string depending on the given value
     *
     * @param bool|null $value
     * @return string
     */
    public static function boolean_as_text(?bool $value): string {
        if ($value === null) {
            return '';
        }
        return $value ? get_string('yes') : get_string('no');
    }

    /**
     * Returns float value as a percentage
     *
     * @param float|null $value
     * @return string
     */
    public static function percent(?float $value): string {
        if ($value === null) {
            return '';
        }
        return get_string('percents', 'moodle', format_float($value));
    }
}
