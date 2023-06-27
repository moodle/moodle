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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to delete user accounts for users who have not completed their profile in time.
 */
class delete_incomplete_users_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskdeleteincompleteusers', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        $timenow = time();

        // Delete users who haven't completed profile within required period.
        if (!empty($CFG->deleteincompleteusers)) {
            $cuttime = $timenow - ($CFG->deleteincompleteusers * 3600);
            $rs = $DB->get_recordset_sql ("SELECT *
                                               FROM {user}
                                           WHERE confirmed = 1 AND lastaccess > 0
                                               AND lastaccess < ? AND deleted = 0
                                               AND (lastname = '' OR firstname = '' OR email = '')",
                                           array($cuttime));
            foreach ($rs as $user) {
                if (isguestuser($user) or is_siteadmin($user)) {
                    continue;
                }
                if ($user->lastname !== '' and $user->firstname !== '' and $user->email !== '') {
                    // This can happen on MySQL - see MDL-52831.
                    continue;
                }
                delete_user($user);
                mtrace(" Deleted not fully setup user $user->username ($user->id)");
            }
            $rs->close();
        }
    }

}
