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
 * A schedule task for scheduled allocation cron.
 *
 * @package   workshopallocation_scheduled
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace workshopallocation_scheduled\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The main schedule task for scheduled allocation cron.
 *
 * @package   workshopallocation_scheduled
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'workshopallocation_scheduled');
    }

    /**
     * Run scheduled allocation cron.
     */
    public function execute() {
        global $CFG, $DB;

        $sql = "SELECT w.*
                  FROM {workshopallocation_scheduled} a
                  JOIN {workshop} w ON a.workshopid = w.id
                 WHERE a.enabled = 1
                   AND w.phase = 20
                   AND w.submissionend > 0
                   AND w.submissionend < ?
                   AND (a.timeallocated IS NULL OR a.timeallocated < w.submissionend)";
        $workshops = $DB->get_records_sql($sql, array(time()));

        if (empty($workshops)) {
            mtrace('... no workshops awaiting scheduled allocation. ', '');
            return;
        }

        mtrace('... executing scheduled allocation in ' . count($workshops) . ' workshop(s) ... ', '');

        require_once($CFG->dirroot . '/mod/workshop/locallib.php');

        foreach ($workshops as $workshop) {
            $cm = get_coursemodule_from_instance('workshop', $workshop->id, $workshop->course, false, MUST_EXIST);
            $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
            $workshop = new \workshop($workshop, $cm, $course);
            $allocator = $workshop->allocator_instance('scheduled');
            $allocator->execute();
        }
    }
}
