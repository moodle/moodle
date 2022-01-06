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
 * Google Meet task - Send notification.
 *
 * @package     mod_googlemeet
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_googlemeet\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/googlemeet/locallib.php');

/**
 * Send notification about the start of the event.
 *
 * @package     mod_googlemeet
 * @category    external
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_event extends \core\task\scheduled_task {
    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('notifytask', 'mod_googlemeet');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $events = googlemeet_get_future_events();

        if ($events) {
            foreach ($events as $event) {
                $users = googlemeet_get_users_to_notify($event->id);

                foreach ($users as $user) {
                    googlemeet_send_notification($user, $event);

                    googlemeet_notify_done($user->id, $event->id);
                }
            }
        }

        googlemeet_remove_notify_done_from_old_events();
    }
}
