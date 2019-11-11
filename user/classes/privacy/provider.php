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
 * Privacy class for requesting user data.
 *
 * @package    core_user
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    core_comment
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\subsystem\provider {

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection) : collection {
        $userfields = [
            'id' => 'privacy:metadata:id',
            'auth' => 'privacy:metadata:auth',
            'confirmed' => 'privacy:metadata:confirmed',
            'policyagreed' => 'privacy:metadata:policyagreed',
            'deleted' => 'privacy:metadata:deleted',
            'suspended' => 'privacy:metadata:suspended',
            'mnethostid' => 'privacy:metadata:mnethostid',
            'username' => 'privacy:metadata:username',
            'password' => 'privacy:metadata:password',
            'idnumber' => 'privacy:metadata:idnumber',
            'firstname' => 'privacy:metadata:firstname',
            'lastname' => 'privacy:metadata:lastname',
            'email' => 'privacy:metadata:email',
            'emailstop' => 'privacy:metadata:emailstop',
            'icq' => 'privacy:metadata:icq',
            'skype' => 'privacy:metadata:skype',
            'yahoo' => 'privacy:metadata:yahoo',
            'aim' => 'privacy:metadata:aim',
            'msn' => 'privacy:metadata:msn',
            'phone1' => 'privacy:metadata:phone',
            'phone2' => 'privacy:metadata:phone',
            'institution' => 'privacy:metadata:institution',
            'department' => 'privacy:metadata:department',
            'address' => 'privacy:metadata:address',
            'city' => 'privacy:metadata:city',
            'country' => 'privacy:metadata:country',
            'lang' => 'privacy:metadata:lang',
            'calendartype' => 'privacy:metadata:calendartype',
            'theme' => 'privacy:metadata:theme',
            'timezone' => 'privacy:metadata:timezone',
            'firstaccess' => 'privacy:metadata:firstaccess',
            'lastaccess' => 'privacy:metadata:lastaccess',
            'lastlogin' => 'privacy:metadata:lastlogin',
            'currentlogin' => 'privacy:metadata:currentlogin',
            'lastip' => 'privacy:metadata:lastip',
            'secret' => 'privacy:metadata:secret',
            'picture' => 'privacy:metadata:picture',
            'url' => 'privacy:metadata:url',
            'description' => 'privacy:metadata:description',
            'maildigest' => 'privacy:metadata:maildigest',
            'maildisplay' => 'privacy:metadata:maildisplay',
            'autosubscribe' => 'privacy:metadata:autosubscribe',
            'trackforums' => 'privacy:metadata:trackforums',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'trustbitmask' => 'privacy:metadata:trustbitmask',
            'imagealt' => 'privacy:metadata:imagealt',
            'lastnamephonetic' => 'privacy:metadata:lastnamephonetic',
            'firstnamephonetic' => 'privacy:metadata:firstnamephonetic',
            'middlename' => 'privacy:metadata:middlename',
            'alternatename' => 'privacy:metadata:alternatename'
        ];

        $passwordhistory = [
            'userid' => 'privacy:metadata:userid',
            'hash' => 'privacy:metadata:hash',
            'timecreated' => 'privacy:metadata:timecreated'
        ];

        $lastaccess = [
            'userid' => 'privacy:metadata:userid',
            'courseid' => 'privacy:metadata:courseid',
            'timeaccess' => 'privacy:metadata:timeaccess'
        ];

        $userpasswordresets = [
            'userid' => 'privacy:metadata:userid',
            'timerequested' => 'privacy:metadata:timerequested',
            'timererequested' => 'privacy:metadata:timererequested',
            'token' => 'privacy:metadata:token'
        ];

        $userdevices = [
            'userid' => 'privacy:metadata:userid',
            'appid' => 'privacy:metadata:appid',
            'name' => 'privacy:metadata:devicename',
            'model' => 'privacy:metadata:model',
            'platform' => 'privacy:metadata:platform',
            'version' => 'privacy:metadata:version',
            'pushid' => 'privacy:metadata:pushid',
            'uuid' => 'privacy:metadata:uuid',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified'
        ];

        $usersessions = [
            'state' => 'privacy:metadata:state',
            'sid' => 'privacy:metadata:sid',
            'userid' => 'privacy:metadata:userid',
            'sessdata' => 'privacy:metadata:sessdata',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'firstip' => 'privacy:metadata:firstip',
            'lastip' => 'privacy:metadata:lastip'
        ];

        $courserequest = [
            'fullname' => 'privacy:metadata:fullname',
            'shortname' => 'privacy:metadata:shortname',
            'summary' => 'privacy:metadata:summary',
            'category' => 'privacy:metadata:category',
            'reason' => 'privacy:metadata:reason',
            'requester' => 'privacy:metadata:requester'
        ];

        $mypages = [
            'userid' => 'privacy:metadata:my_pages:userid',
            'name' => 'privacy:metadata:my_pages:name',
            'private' => 'privacy:metadata:my_pages:private',
        ];

        $userpreferences = [
            'userid' => 'privacy:metadata:user_preferences:userid',
            'name' => 'privacy:metadata:user_preferences:name',
            'value' => 'privacy:metadata:user_preferences:value'
        ];

        $collection->add_database_table('user', $userfields, 'privacy:metadata:usertablesummary');
        $collection->add_database_table('user_password_history', $passwordhistory, 'privacy:metadata:passwordtablesummary');
        $collection->add_database_table('user_password_resets', $userpasswordresets, 'privacy:metadata:passwordresettablesummary');
        $collection->add_database_table('user_lastaccess', $lastaccess, 'privacy:metadata:lastaccesstablesummary');
        $collection->add_database_table('user_devices', $userdevices, 'privacy:metadata:devicetablesummary');
        $collection->add_database_table('course_request', $courserequest, 'privacy:metadata:requestsummary');
        $collection->add_database_table('sessions', $usersessions, 'privacy:metadata:sessiontablesummary');
        $collection->add_database_table('my_pages', $mypages, 'privacy:metadata:my_pages');
        $collection->add_database_table('user_preferences', $userpreferences, 'privacy:metadata:user_preferences');
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:filelink');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $params = ['userid' => $userid, 'contextuser' => CONTEXT_USER];
        $sql = "SELECT id
                  FROM {context}
                 WHERE instanceid = :userid and contextlevel = :contextuser";
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $userlist->add_user($context->instanceid);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $context = $contextlist->current();
        $user = \core_user::get_user($contextlist->get_user()->id);
        static::export_user($user, $context);
        static::export_password_history($user->id, $context);
        static::export_password_resets($user->id, $context);
        static::export_lastaccess($user->id, $context);
        static::export_course_requests($user->id, $context);
        static::export_user_devices($user->id, $context);
        static::export_user_session_data($user->id, $context);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Only delete data for a user context.
        if ($context->contextlevel == CONTEXT_USER) {
            static::delete_user_data($context->instanceid, $context);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {

        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_user_data($context->instanceid, $context);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        foreach ($contextlist as $context) {
            // Let's be super certain that we have the right information for this user here.
            if ($context->contextlevel == CONTEXT_USER && $contextlist->get_user()->id == $context->instanceid) {
                static::delete_user_data($contextlist->get_user()->id, $contextlist->current());
            }
        }
    }

    /**
     * Deletes non vital information about a user.
     *
     * @param  int      $userid  The user ID to delete
     * @param  \context $context The user context
     */
    protected static function delete_user_data(int $userid, \context $context) {
        global $DB;

        // Delete password history.
        $DB->delete_records('user_password_history', ['userid' => $userid]);
        // Delete last access.
        $DB->delete_records('user_lastaccess', ['userid' => $userid]);
        // Delete password resets.
        $DB->delete_records('user_password_resets', ['userid' => $userid]);
        // Delete user devices.
        $DB->delete_records('user_devices', ['userid' => $userid]);
        // Delete user course requests.
        $DB->delete_records('course_request', ['requester' => $userid]);
        // Delete sessions.
        $DB->delete_records('sessions', ['userid' => $userid]);
        // Do I delete user preferences? Seems like the right place to do it.
        $DB->delete_records('user_preferences', ['userid' => $userid]);

        // Delete all of the files for this user.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'user');

        // For the user record itself we only want to remove unnecessary data. We still need the core data to keep as a record
        // that we actually did follow the request to be forgotten.
        $user = \core_user::get_user($userid);
        // Update fields we wish to change to nothing.
        $user->deleted = 1;
        $user->idnumber = '';
        $user->emailstop = 0;
        $user->icq = '';
        $user->skype = '';
        $user->yahoo = '';
        $user->aim = '';
        $user->msn = '';
        $user->phone1 = '';
        $user->phone2 = '';
        $user->institution = '';
        $user->department = '';
        $user->address = '';
        $user->city = '';
        $user->country = '';
        $user->lang = '';
        $user->calendartype = '';
        $user->theme = '';
        $user->timezone = '';
        $user->firstaccess = 0;
        $user->lastaccess = 0;
        $user->lastlogin = 0;
        $user->currentlogin = 0;
        $user->lastip = 0;
        $user->secret = '';
        $user->picture = '';
        $user->url = '';
        $user->description = '';
        $user->descriptionformat = 0;
        $user->mailformat = 0;
        $user->maildigest = 0;
        $user->maildisplay = 0;
        $user->autosubscribe = 0;
        $user->trackforums = 0;
        $user->timecreated = 0;
        $user->timemodified = 0;
        $user->trustbitmask = 0;
        $user->imagealt = '';
        $user->lastnamephonetic = '';
        $user->firstnamephonetic = '';
        $user->middlename = '';
        $user->alternatename = '';
        $DB->update_record('user', $user);
    }

    /**
     * Export core user data.
     *
     * @param  \stdClass $user The user object.
     * @param  \context $context The user context.
     */
    protected static function export_user(\stdClass $user, \context $context) {
        $data = (object) [
            'auth' => $user->auth,
            'confirmed' => transform::yesno($user->confirmed),
            'policyagreed' => transform::yesno($user->policyagreed),
            'deleted' => transform::yesno($user->deleted),
            'suspended' => transform::yesno($user->suspended),
            'username' => $user->username,
            'idnumber' => $user->idnumber,
            'firstname' => format_string($user->firstname, true, ['context' => $context]),
            'lastname' => format_string($user->lastname, true, ['context' => $context]),
            'email' => $user->email,
            'emailstop' => transform::yesno($user->emailstop),
            'icq' => format_string($user->icq, true, ['context' => $context]),
            'skype' => format_string($user->skype, true, ['context' => $context]),
            'yahoo' => format_string($user->yahoo, true, ['context' => $context]),
            'aim' => format_string($user->aim, true, ['context' => $context]),
            'msn' => format_string($user->msn, true, ['context' => $context]),
            'phone1' => format_string($user->phone1, true, ['context' => $context]),
            'phone2' => format_string($user->phone2, true, ['context' => $context]),
            'institution' => format_string($user->institution, true, ['context' => $context]),
            'department' => format_string($user->department, true, ['context' => $context]),
            'address' => format_string($user->address, true, ['context' => $context]),
            'city' => format_string($user->city, true, ['context' => $context]),
            'country' => format_string($user->country, true, ['context' => $context]),
            'lang' => $user->lang,
            'calendartype' => $user->calendartype,
            'theme' => $user->theme,
            'timezone' => $user->timezone,
            'firstaccess' => $user->firstaccess ? transform::datetime($user->firstaccess) : null,
            'lastaccess' => $user->lastaccess ? transform::datetime($user->lastaccess) : null,
            'lastlogin' => $user->lastlogin ? transform::datetime($user->lastlogin) : null,
            'currentlogin' => $user->currentlogin ? transform::datetime($user->currentlogin) : null,
            'lastip' => $user->lastip,
            'secret' => $user->secret,
            'picture' => $user->picture,
            'url' => $user->url,
            'description' => format_text($user->description, $user->descriptionformat, ['context' => $context]),
            'maildigest' => transform::yesno($user->maildigest),
            'maildisplay' => $user->maildisplay,
            'autosubscribe' => transform::yesno($user->autosubscribe),
            'trackforums' => transform::yesno($user->trackforums),
            'timecreated' => transform::datetime($user->timecreated),
            'timemodified' => transform::datetime($user->timemodified),
            'imagealt' => format_string($user->imagealt, true, ['context' => $context]),
            'lastnamephonetic' => format_string($user->lastnamephonetic, true, ['context' => $context]),
            'firstnamephonetic' => format_string($user->firstnamephonetic, true, ['context' => $context]),
            'middlename' => format_string($user->middlename, true, ['context' => $context]),
            'alternatename'  => format_string($user->alternatename, true, ['context' => $context])
        ];
        if (isset($data->description)) {
            $data->description = writer::with_context($context)->rewrite_pluginfile_urls(
                    [get_string('privacy:descriptionpath', 'user')], 'user', 'profile', '', $data->description);
        }
        writer::with_context($context)->export_area_files([], 'user', 'profile', 0)
                ->export_data([], $data);
        // Export profile images.
        writer::with_context($context)->export_area_files([get_string('privacy:profileimagespath', 'user')], 'user', 'icon', 0);
        // Export private files.
        writer::with_context($context)->export_area_files([get_string('privacy:privatefilespath', 'user')], 'user', 'private', 0);
        // Export draft files.
        writer::with_context($context)->export_area_files([get_string('privacy:draftfilespath', 'user')], 'user', 'draft', false);
    }

    /**
     * Export information about the last time a user accessed a course.
     *
     * @param  int $userid The user ID.
     * @param  \context $context The user context.
     */
    protected static function export_lastaccess(int $userid, \context $context) {
        global $DB;
        $sql = "SELECT c.id, c.fullname, ul.timeaccess
                  FROM {user_lastaccess} ul
                  JOIN {course} c ON c.id = ul.courseid
                 WHERE ul.userid = :userid";
        $params = ['userid' => $userid];
        $records = $DB->get_records_sql($sql, $params);
        if (!empty($records)) {
            $lastaccess = (object) array_map(function($record) use ($context) {
                return [
                    'course_name' => format_string($record->fullname, true, ['context' => $context]),
                    'timeaccess' => transform::datetime($record->timeaccess)
                ];
            }, $records);
            writer::with_context($context)->export_data([get_string('privacy:lastaccesspath', 'user')], $lastaccess);
        }
    }

    /**
     * Exports information about password resets.
     *
     * @param  int $userid The user ID
     * @param  \context $context Context for this user.
     */
    protected static function export_password_resets(int $userid, \context $context) {
        global $DB;
        $records = $DB->get_records('user_password_resets', ['userid' => $userid]);
        if (!empty($records)) {
            $passwordresets = (object) array_map(function($record) {
                return [
                    'timerequested' => transform::datetime($record->timerequested),
                    'timererequested' => transform::datetime($record->timererequested)
                ];
            }, $records);
            writer::with_context($context)->export_data([get_string('privacy:passwordresetpath', 'user')], $passwordresets);
        }
    }

    /**
     * Exports information about the user's mobile devices.
     *
     * @param  int $userid The user ID.
     * @param  \context $context Context for this user.
     */
    protected static function export_user_devices(int $userid, \context $context) {
        global $DB;
        $records = $DB->get_records('user_devices', ['userid' => $userid]);
        if (!empty($records)) {
            $userdevices = (object) array_map(function($record) {
                return [
                    'appid' => $record->appid,
                    'name' => $record->name,
                    'model' => $record->model,
                    'platform' => $record->platform,
                    'version' => $record->version,
                    'timecreated' => transform::datetime($record->timecreated),
                    'timemodified' => transform::datetime($record->timemodified)
                ];
            }, $records);
            writer::with_context($context)->export_data([get_string('privacy:devicespath', 'user')], $userdevices);
        }
    }

    /**
     * Exports information about course requests this user made.
     *
     * @param  int    $userid  The user ID.
     * @param  \context $context The context object
     */
    protected static function export_course_requests(int $userid, \context $context) {
        global $DB;
        $sql = "SELECT cr.shortname, cr.fullname, cr.summary, cc.name AS category, cr.reason
                  FROM {course_request} cr
                  JOIN {course_categories} cc ON cr.category = cc.id
                 WHERE cr.requester = :userid";
        $params = ['userid' => $userid];
        $records = $DB->get_records_sql($sql, $params);
        if ($records) {
            writer::with_context($context)->export_data([get_string('privacy:courserequestpath', 'user')], (object) $records);
        }
    }

    /**
     * Get details about the user's password history.
     *
     * @param int $userid The user ID that we are getting the password history for.
     * @param \context $context the user context.
     */
    protected static function export_password_history(int $userid, \context $context) {
        global $DB;

        // Just provide a count of how many entries we have.
        $recordcount = $DB->count_records('user_password_history', ['userid' => $userid]);
        if ($recordcount) {
            $passwordhistory = (object) ['password_history_count' => $recordcount];
            writer::with_context($context)->export_data([get_string('privacy:passwordhistorypath', 'user')], $passwordhistory);
        }
    }

    /**
     * Exports information about the user's session.
     *
     * @param  int $userid The user ID.
     * @param  \context $context The context for this user.
     */
    protected static function export_user_session_data(int $userid, \context $context) {
        global $DB, $SESSION;

        $records = $DB->get_records('sessions', ['userid' => $userid]);
        if (!empty($records)) {
            $sessiondata = (object) array_map(function($record) {
                return [
                    'state' => $record->state,
                    'sessdata' => base64_decode($record->sessdata),
                    'timecreated' => transform::datetime($record->timecreated),
                    'timemodified' => transform::datetime($record->timemodified),
                    'firstip' => $record->firstip,
                    'lastip' => $record->lastip
                ];
            }, $records);
            writer::with_context($context)->export_data([get_string('privacy:sessionpath', 'user')], $sessiondata);
        }
    }
}
