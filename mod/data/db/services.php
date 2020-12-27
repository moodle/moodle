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
 * Database external functions and service definitions.
 *
 * @package    mod_data
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

$functions = array(

    'mod_data_get_databases_by_courses' => array(
        'classname' => 'mod_data_external',
        'methodname' => 'get_databases_by_courses',
        'description' => 'Returns a list of database instances in a provided set of courses, if
            no courses are provided then all the database instances the user has access to will be returned.',
        'type' => 'read',
        'capabilities' => 'mod/data:viewentry',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_view_database' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'view_database',
        'description'   => 'Simulate the view.php web interface data: trigger events, completion, etc...',
        'type'          => 'write',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_get_data_access_information' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'get_data_access_information',
        'description'   => 'Return access information for a given database.',
        'type'          => 'read',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_get_entries' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'get_entries',
        'description'   => 'Return the complete list of entries of the given database.',
        'type'          => 'read',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_get_entry' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'get_entry',
        'description'   => 'Return one entry record from the database, including contents optionally.',
        'type'          => 'read',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_get_fields' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'get_fields',
        'description'   => 'Return the list of configured fields for the given database.',
        'type'          => 'read',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_search_entries' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'search_entries',
        'description'   => 'Search for entries in the given database.',
        'type'          => 'read',
        'capabilities'  => 'mod/data:viewentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_approve_entry' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'approve_entry',
        'description'   => 'Approves or unapproves an entry.',
        'type'          => 'write',
        'capabilities'  => 'mod/data:approve',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_delete_entry' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'delete_entry',
        'description'   => 'Deletes an entry.',
        'type'          => 'write',
        'capabilities'  => 'mod/data:manageentries',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_add_entry' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'add_entry',
        'description'   => 'Adds a new entry.',
        'type'          => 'write',
        'capabilities'  => 'mod/data:writeentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_data_update_entry' => array(
        'classname'     => 'mod_data_external',
        'methodname'    => 'update_entry',
        'description'   => 'Updates an existing entry.',
        'type'          => 'write',
        'capabilities'  => 'mod/data:writeentry',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
