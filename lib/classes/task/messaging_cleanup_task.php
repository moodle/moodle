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
 * Simple task to delete old messaging records.
 */
class messaging_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskmessagingcleanup', 'admin');
    }

    /**
     * Do the job. Each message processor also gets the chance to perform it's own cleanup.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        $timenow = time();

        $processors = get_message_processors(true);

        // Cleanup read and unread notifications.
        if (!empty($CFG->messagingdeleteallnotificationsdelay)) {
            $notificationdeletetime = $timenow - $CFG->messagingdeleteallnotificationsdelay;

            /** @var \message_output $processor */
            foreach (array_column($processors, 'object') as $processor) {
                $processor->cleanup_all_notifications($notificationdeletetime);
            }

            $params = array('notificationdeletetime' => $notificationdeletetime);
            $DB->delete_records_select('notifications', 'timecreated < :notificationdeletetime', $params);
        }

        // Cleanup read notifications.
        if (!empty($CFG->messagingdeletereadnotificationsdelay)) {
            $notificationdeletetime = $timenow - $CFG->messagingdeletereadnotificationsdelay;

            /** @var \message_output $processor */
            foreach (array_column($processors, 'object') as $processor) {
                $processor->cleanup_read_notifications($notificationdeletetime);
            }

            $params = array('notificationdeletetime' => $notificationdeletetime);
            $DB->delete_records_select('notifications', 'timeread < :notificationdeletetime', $params);
        }
    }
}
