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
 * Helper class locally used.
 *
 * @package    logstore_database
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_database;
defined('MOODLE_INTERNAL') || die();


/**
 * Helper class locally used.
 *
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Returns list of fully working database drivers present in system.
     * @return array
     */
    public static function get_drivers() {
        return array(
            ''               => get_string('choosedots'),
            'native/mysqli'  => \moodle_database::get_driver_instance('mysqli', 'native')->get_name(),
            'native/mariadb' => \moodle_database::get_driver_instance('mariadb', 'native')->get_name(),
            'native/pgsql'   => \moodle_database::get_driver_instance('pgsql', 'native')->get_name(),
            'native/oci'     => \moodle_database::get_driver_instance('oci', 'native')->get_name(),
            'native/sqlsrv'  => \moodle_database::get_driver_instance('sqlsrv', 'native')->get_name(),
            'native/mssql'   => \moodle_database::get_driver_instance('mssql', 'native')->get_name()
        );
    }

    /**
     * Get a list of edu levels.
     *
     * @return array
     */
    public static function get_level_options() {
        return array(
            \core\event\base::LEVEL_TEACHING      => get_string('teaching', 'logstore_database'),
            \core\event\base::LEVEL_PARTICIPATING => get_string('participating', 'logstore_database'),
            \core\event\base::LEVEL_OTHER         => get_string('other', 'logstore_database'),
        );
    }

    /**
     * Get a list of database actions.
     *
     * @return array
     */
    public static function get_action_options() {
        return array(
            'c' => get_string('create', 'logstore_database'),
            'r' => get_string('read', 'logstore_database'),
            'u' => get_string('update', 'logstore_database'),
            'd' => get_string('delete')
        );
    }
}
