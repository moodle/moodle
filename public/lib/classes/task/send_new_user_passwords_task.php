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
 * Scheduled task class.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to create accounts and send password emails for new users.
 */
class send_new_user_passwords_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksendnewuserpasswords', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $DB;

        // Generate new password emails for users - ppl expect these generated asap.
        if (
            $DB->record_exists_select(
                'user_preferences',
                'name = ? AND ' . $DB->sql_compare_text('value', 2) . ' = ?',
                ['create_password', '1']
            )
        ) {
            mtrace('Creating passwords for new users...');
            $userfieldsapi = \core_user\fields::for_name();
            $usernamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            $newusers = $DB->get_recordset_sql("SELECT u.id as id, u.email, u.auth, u.deleted,
                                                     u.suspended, u.emailstop, u.mnethostid, u.mailformat,
                                                     $usernamefields, u.username, u.lang,
                                                     p.id as prefid
                                                FROM {user} u
                                                JOIN {user_preferences} p ON u.id = p.userid
                                               WHERE p.name = 'create_password'
                                                 AND " . $DB->sql_compare_text('p.value', 2) . " = '1'
                                                 AND u.email <> ''
                                                 AND u.suspended = 0
                                                 AND u.auth <> 'nologin'
                                                 AND u.deleted = 0");

            // Note: we can not send emails to suspended accounts.
            foreach ($newusers as $newuser) {
                // Use a low cost factor when generating bcrypt hash otherwise
                // hashing would be slow when emailing lots of users. Hashes
                // will be automatically updated to a higher cost factor the first
                // time the user logs in.
                if (setnew_password_and_mail($newuser, true)) {
                    unset_user_preference('create_password', $newuser);
                    set_user_preference('auth_forcepasswordchange', 1, $newuser);
                } else {
                    trigger_error("Could not create and mail new user password!");
                }
            }
            $newusers->close();
        }
    }

}
