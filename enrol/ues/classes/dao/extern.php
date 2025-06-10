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

/**
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class ues_external extends ues_base {
    public static function by_id($id) {
        return self::get(array('id' => $id));
    }

    public static function get_all($params = array(), $sort = '', $fields = '*', $offset = 0, $limit = 0) {
        return self::get_all_internal($params, $sort, $fields, $offset, $limit);
    }

    public static function get($params, $fields = '*') {
        return current(self::get_all($params, '', $fields));
    }

    public static function delete_all($params = array()) {
        return self::delete_all_internal($params);
    }
}
