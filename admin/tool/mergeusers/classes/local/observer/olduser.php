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
 * Observer for the user_merged_success event for suspending the user to remove.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @author    John Hoopes <hoopes@wisc.edu>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @copyright University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\observer;

use context_user;
use dml_exception;
use tool_mergeusers\event\user_merged_success;

/**
 * Observer for the user_merged_success event for suspending the user to remove.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @author    John Hoopes <hoopes@wisc.edu>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @copyright University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class olduser {
    /**
     * Suspend the old user, by suspending its account, and updating the profile picture
     * to a generic one
     *
     * @param user_merged_success $event Event data.
     * @throws dml_exception
     */
    public static function old_user_suspend(user_merged_success $event): void {
        // 0. Check configuration to see if the old user has to be suspended.
        $suspenduser = (bool) (int) get_config('tool_mergeusers', 'suspenduser');
        if (!$suspenduser) {
            return;
        }

        // Suspend user and update the profile picture.
        global $CFG, $DB;
        require_once($CFG->libdir . '/gdlib.php');

        $useridtoremove = $event->other['usersinvolved']['fromid'];

        // 1. update suspended flag.
        $usertoremove = new \stdClass();
        $usertoremove->id = $useridtoremove;
        $usertoremove->suspended = 1;
        $usertoremove->timemodified = time();
        $DB->update_record('user', $usertoremove);

        // 2. update profile picture.
        // Get source, common image.
        $fullpath = dirname(__DIR__, 2) . "/pix/suspended.jpg";
        if (!file_exists($fullpath)) {
            return; // Do nothing; aborting, given that the image does not exist. This should not happen.
        }

        // Place the common image as the profile picture.
        $context = context_user::instance($useridtoremove);
        if (($newrev = process_new_icon($context, 'user', 'icon', 0, $fullpath))) {
            $DB->set_field('user', 'picture', $newrev, ['id' => $useridtoremove]);
        }
    }
}
