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
 * Privacy Subsystem implementation for block_learnerscript.
 *
 * @package    block_learnerscript
 * @copyright  2018 Jahnavi <jahnavi@eabyas.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_learnerscript\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;

class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table(
            'block_devicels',
             [
                'userid' => 'privacy:metadata:block_devicels:userid',
                'accessip' => 'privacy:metadata:block_devicels:accessip',
                'country' => 'privacy:metadata:block_devicels:country',
                'countrycode' => 'privacy:metadata:block_devicels:countrycode',
                'region' => 'privacy:metadata:block_devicels:region',
                'regionname' => 'privacy:metadata:block_devicels:regionname',
                'city' => 'privacy:metadata:block_devicels:city',
                'browser' => 'privacy:metadata:block_devicels:browser',
                'browserparent' => 'privacy:metadata:block_devicels:browserparent',
                'platform' => 'privacy:metadata:block_devicels:platform',
                'browserversion' => 'privacy:metadata:block_devicels:browserversion',
                'devicetype' => 'privacy:metadata:block_devicels:devicetype',
                'pointingmethod' => 'privacy:metadata:block_devicels:pointingmethod',
                'ismobiledevice' => 'privacy:metadata:block_devicels:ismobiledevice',
                'istablet' => 'privacy:metadata:block_devicels:istablet',
                'timemodified' => 'privacy:metadata:block_devicels:timemodified',
             ],
            'privacy:metadata:block_devicels'
        );

        return $collection;
    }
    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        return new contextlist();
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }
}
