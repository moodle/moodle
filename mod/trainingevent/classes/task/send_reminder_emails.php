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
 * Send reminder emails Task
 *
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_trainingevent\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Send reminder emails Task
 *
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_reminder_emails extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('sendreminderemails', 'mod_trainingevent');
    }

    /**
     * Execute trainingevent cron tasks.
     */
    public function execute() {
        global $CFG, $DB;

        $runtime = time();

        // Get all of the training events which have reminders set and not already passed.
        if ($trainingevents = $DB->get_records_sql("SELECT * FROM {trainingevent}
                                                    WHERE sendreminder > 0
                                                    AND startdatetime < :now",
                                                    ['now' => $runtime])) {
            foreach ($trainingevents as $trainingevent) {
                // Do we need to do anything?
                if ((($trainingevent->sendreminder + 1 ) * 24 * 60 * 60 + $runtime) > $trainingevent->startdate &&
                    ($trainingevent->sendreminder *24 * 60 *60 + $runtime) < $trainingevent->startdate ) {
                        // Get all of the users for this event.
                        $eventusers = $DB->get_records('trainingevent_users', ['trainingeventid' => $trainingevent->id, 'waitlisted' => 0]);

                        // Is anyone signed up?
                        if (empty($eventusers)) {
                            continue;
                        }

                        // Does the course actually exist?
                        if (!$course = $DB->get_record('course', ['id' => $trainingevent->course])) {
                            continue;
                        }

                        // How about the location?
                        if (!$location = $DB->get_record('classroom', array('id' => $event->classroomid))) {
                            continue;
                        }
                        $location->time = date($CFG->iomad_date_format . ' \a\t H:i', $event->startdatetime);

                        // Send the reminders.
                        foreach ($eventusers as $eventuser) {
                            if ($user = $DB->get_record('user', ['id' => $eventuser->userid, 'suspended' => 0, 'deleted' => 0])) {
                                // Get the user's company.
                                $usercompany = \company::by_userid($user->id);
                                EmailTemplate::send('user_signed_up_for_event_reminder', array('course' => $course,
                                                                                               'user' => $USER,
                                                                                               'classroom' => $location,
                                                                                               'company' => $usercompany,
                                                                                               'event' => $event));

                            }
                        }
                    }
            }
        }
    }
}
