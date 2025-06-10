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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');
require_login();

defined('MOODLE_INTERNAL') || die();

class lsuxe {

    /**
     * Master function for FULL lsuxe enrollment and processing.
     *
     * @return boolean
     */
    public function run_lsuxe_full_enroll() {
        $parms = array(
            'intervals' => 'true',
            'courseid' => '1',
            'moodleid' => '0',
            'function' => 'full'
        );

        $starttime = microtime(true);

        if (lsuxe_helpers::is_ues()) {
            mtrace("Using LSU UES");
        } else {
            mtrace("Normal Moodle Enrollment");
        }

        lsuxe_helpers::xe_write_destcourse($parms);

        $groups = lsuxe_helpers::xe_get_groups($parms);

        lsuxe_helpers::xe_write_destgroups($groups);

        $users = lsuxe_helpers::xe_current_enrollments($parms);

        $count = 0;
        foreach ($users as $user) {
            $count++;

            $userstarttime = microtime(true);
            $remoteuser = lsuxe_helpers::xe_remote_user_lookup($user);
            if (isset($remoteuser['id'])) {
                $usermatch = lsuxe_helpers::xe_remote_user_match($user, $remoteuser);
                if (!$usermatch) {
                    $updateuser = lsuxe_helpers::xe_remote_user_update($user, $remoteuser);
                }
            } else {
                $createduser = lsuxe_helpers::xe_remote_user_create($user);

                $remoteuser = $createduser;
            }

            if ($user->status == 'enrolled') {
                $enrolluser = lsuxe_helpers::xe_enroll_user($user, $remoteuser['id']);
                $enrolgroup = lsuxe_helpers::xe_add_user_to_group($user, $remoteuser['id']);
            } else if ($user->status == 'unenrolled') {
                $enrolluser = lsuxe_helpers::xe_unenroll_user($user, $remoteuser['id']);
            }

            $userelapsedtime = round(microtime(true) - $userstarttime, 3);

            lsuxe_helpers::processed($user->xemmid);

            mtrace("User #$count ($user->username) took " . $userelapsedtime . " seconds to process.\n");
        }

        $elapsedtime = round(microtime(true) - $starttime, 3);
        mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
    }


    /**
     * Master function for grabbing remote course data and populating it locally.
     *
     * @return boolean
     */
    public function run_lsuxe_courses() {
        $starttime = microtime(true);

        if (lsuxe_helpers::is_ues()) {
            mtrace("Using LSU UES");
        } else {
            mtrace("Normal Moodle Enrollment");
        }

        lsuxe_helpers::xe_write_destcourse();

        $elapsedtime = round(microtime(true) - $starttime, 3);
        mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");

        return true;
    }

    /**
     * Master function for remote userid lookup, remote creation, and local storage.
     *
     * @return boolean
     */
    public function run_lsuxe_users() {
        $starttime = microtime(true);

        if (lsuxe_helpers::is_ues()) {
            mtrace("Using LSU UES");
        } else {
            mtrace("Normal Moodle Enrollment");
        }

        $users = lsuxe_helpers::xe_current_enrollments(true);

        $count = 0;
        foreach ($users as $user) {
            $count++;

            $userstarttime = microtime(true);
            $remoteuser = lsuxe_helpers::xe_remote_user_lookup($user);
            if (isset($remoteuser['id'])) {
                $usermatch = lsuxe_helpers::xe_remote_user_match($user, $remoteuser);
                if (!$usermatch) {
                    $updateuser = lsuxe_helpers::xe_remote_user_update($user, $remoteuser);
                }
            } else {
                $createduser = lsuxe_helpers::xe_remote_user_create($user);
                $remoteuser = $createduser;
            }

            $userelapsedtime = round(microtime(true) - $userstarttime, 3);
            mtrace("User #$count ($user->username) took " . $userelapsedtime . " seconds to process.\n");
        }

        $elapsedtime = round(microtime(true) - $starttime, 3);
        mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
    }


    /**
     * Master function for remote group lookup and local storage.
     *
     * @return boolean
     */
    public function run_lsuxe_groups() {
        $starttime = microtime(true);

        if (lsuxe_helpers::is_ues()) {
            mtrace("Using LSU UES");
        } else {
            mtrace("Normal Moodle Enrollment");
        }

        $groups = lsuxe_helpers::xe_get_groups();

        lsuxe_helpers::xe_write_destgroups($groups);

        $elapsedtime = round(microtime(true) - $starttime, 3);
        mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
    }

    /**
     * Master function for lsuxe enrollment and processing.
     *
     * @return boolean
     */
    public function run_lsuxe_enroll() {

        $starttime = microtime(true);

        if (lsuxe_helpers::is_ues()) {
            mtrace("Using LSU UES");
        } else {
            mtrace("Normal Moodle Enrollment");
        }

        $users = lsuxe_helpers::xe_current_enrollments(true);

        $count = 0;
        foreach ($users as $user) {
            $count++;

            $userstarttime = microtime(true);

            $userstarttime = microtime(true);

            $remoteuser = lsuxe_helpers::xe_remote_user_lookup($user);

            if (isset($remoteuser['id'])) {
                if ($user->status == 'enrolled') {
                    $enrolluser = lsuxe_helpers::xe_enroll_user($user, $remoteuser['id']);
                    $enrolgroup = lsuxe_helpers::xe_add_user_to_group($user, $remoteuser['id']);
                } else if ($user->status == 'unenrolled') {
                    $enrolluser = lsuxe_helpers::xe_unenroll_user($user, $remoteuser['id']);
                }
            } else {
                mtrace("ERROR: $user->username does not exist on the remote server.");
            }

            $userelapsedtime = round(microtime(true) - $userstarttime, 3);

            lsuxe_helpers::processed($user->xemmid);

            mtrace("User #$count ($user->username) took " . $userelapsedtime . " seconds to process.\n");
        }

        $elapsedtime = round(microtime(true) - $starttime, 3);
        mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
    }

}
