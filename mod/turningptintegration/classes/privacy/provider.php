<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by // the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
namespace mod_turningptintegration\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\request\core_userlist_provider,
                          \core_privacy\local\metadata\provider,
                          \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection) : collection {
        $collection->add_subsystem_link('core_grading', [], 'turningptintegration:privacy:grading');

        $collection->add_external_location_link('turningtechnologies.com', [
            'users_courses' => 'privacy:metadata:turningptintegration:users_courses',
            'course_participants' => 'privacy:metadata:turningptintegration:course_participants'
        ], 'privacy:metadata:turningptintegration:externalpurpose');

        return $collection;
    }

    public static function get_contexts_for_userid(int $userid) : contextlist {
        return new contextlist();
    }

    public static function export_user_data(approved_contextlist $contextlist) {
        //All GDPR requests for data removal from TurningPoint are serviced manually, as per instructions
        //in string privacy:metadata:turningptintegration:externalpurpose.
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        //All GDPR requests for data removal from TurningPoint are serviced manually, as per instructions
        //in string privacy:metadata:turningptintegration:externalpurpose.
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        //All GDPR requests for data removal from TurningPoint are serviced manually, as per instructions
        //in string privacy:metadata:turningptintegration:externalpurpose.
    }

    public static function get_users_in_context(userlist $userlist) {
        //All GDPR requests for data removal from TurningPoint are serviced manually, as per instructions
        //in string privacy:metadata:turningptintegration:externalpurpose.
    }

    public static function delete_data_for_users(approved_userlist $userlist) {
        //All GDPR requests for data removal from TurningPoint are serviced manually, as per instructions
        //in string privacy:metadata:turningptintegration:externalpurpose.
    }
 }
