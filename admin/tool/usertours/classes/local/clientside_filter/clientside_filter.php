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

namespace tool_usertours\local\clientside_filter;

use stdClass;
use tool_usertours\local\filter\base;
use tool_usertours\tour;

/**
 * Clientside filter base.
 *
 * @package    tool_usertours
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class clientside_filter extends base {
    /**
     * Returns the filter values needed for client side filtering.
     *
     * @param   tour            $tour       The tour to find the filter values for
     * @return  stdClass
     */
    public static function get_client_side_values(tour $tour): stdClass {
        $data = (object) [];

        if (is_a(static::class, self::class, true)) {
            $data->filterdata = $tour->get_filter_values(static::get_filter_name());
        }

        return $data;
    }
}
