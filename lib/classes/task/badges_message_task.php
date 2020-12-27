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
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run the badges cron.
 */
class badges_message_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskbadgesmessagecron', 'admin');
    }

    /**
     * Reviews criteria and awards badges
     *
     * First find all badges that can be earned, then reviews each badge.
     */
    public function execute() {
        global $CFG, $DB;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->libdir . '/badgeslib.php');
            mtrace('Sending scheduled badge notifications.');

            $scheduled = $DB->get_records_select('badge', 'notification > ? AND (status != ?) AND nextcron < ?',
                array(BADGE_MESSAGE_ALWAYS, BADGE_STATUS_ARCHIVED, time()),
                'notification ASC', 'id, name, notification, usercreated as creator, timecreated');

            foreach ($scheduled as $sch) {
                // Send messages.
                badge_assemble_notification($sch);

                // Update next cron value.
                $nextcron = badges_calculate_message_schedule($sch->notification);
                $DB->set_field('badge', 'nextcron', $nextcron, array('id' => $sch->id));
            }
        }
    }

}
