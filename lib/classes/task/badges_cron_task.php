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
 * Simple task to run the badges cron.
 */
class badges_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskbadgescron', 'admin');
    }

    /**
     * Reviews criteria and awards badges
     *
     * First find all badges that can be earned, then reviews each badge.
     * (Not sure how efficient this is timewise).
     */
    public function execute() {
        global $DB, $CFG;
        if (empty($CFG->enablebadges)) {
            return;
        }
        require_once($CFG->libdir . '/badgeslib.php');

        $courseparams = [];
        if (empty($CFG->badges_allowcoursebadges)) {
            $coursesql = '';
        } else {
            $coursesql = "OR EXISTS (
                          SELECT c.id
                            FROM {course} c
                           WHERE c.visible = :visible
                             AND c.startdate < :current
                             AND c.id = b.courseid
                           ) ";
            $courseparams = ['visible' => 1, 'current' => time()];
        }

        $sql = "SELECT b.id
                  FROM {badge} b
                 WHERE (b.status = :active OR b.status = :activelocked)
                   AND (b.type = :site $coursesql )";
        $badgeparams = [
            'active' => BADGE_STATUS_ACTIVE,
            'activelocked' => BADGE_STATUS_ACTIVE_LOCKED,
            'site' => BADGE_TYPE_SITE,
        ];
        $params = array_merge($badgeparams, $courseparams);
        $badges = $DB->get_fieldset_sql($sql, $params);

        foreach ($badges as $bid) {
            $task = new badges_adhoc_task();
            $task->set_custom_data(['badgeid' => $bid]);
            manager::queue_adhoc_task($task, true);
        }

        mtrace(count($badges) . " adhoc badge tasks were added");
    }
}
