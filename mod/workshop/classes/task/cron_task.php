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
 * A scheduled task for workshop cron.
 *
 * @package    mod_workshop
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_workshop\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The main scheduled task for the workshop.
 *
 * @package   mod_workshop
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
        return get_string('crontask', 'mod_workshop');
    }

    /**
     * Run workshop cron.
     */
    public function execute() {
        global $CFG, $DB;

        $now = time();

        mtrace(' processing workshop subplugins ...');

        // Now when the scheduled allocator had a chance to do its job.
        // Check if there are some workshops to switch into the assessment phase.
        $workshops = $DB->get_records_select("workshop",
            "phase = 20 AND phaseswitchassessment = 1 AND submissionend > 0 AND submissionend < ?", [$now]);

        if (!empty($workshops)) {
            mtrace('Processing automatic assessment phase switch in ' . count($workshops) . ' workshop(s) ... ', '');
            require_once($CFG->dirroot . '/mod/workshop/locallib.php');
            foreach ($workshops as $workshop) {
                $cm = get_coursemodule_from_instance('workshop', $workshop->id, $workshop->course, false, MUST_EXIST);
                $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
                $workshop = new \workshop($workshop, $cm, $course);
                $workshop->switch_phase(\workshop::PHASE_ASSESSMENT);

                $params = [
                    'objectid' => $workshop->id,
                    'context' => $workshop->context,
                    'courseid' => $workshop->course->id,
                    'other' => [
                        'workshopphase' => $workshop->phase
                    ]
                ];
                $event = \mod_workshop\event\phase_switched::create($params);
                $event->trigger();

                // Disable the automatic switching now so that it is not executed again by accident.
                // That can happen if the teacher changes the phase back to the submission one.
                $DB->set_field('workshop', 'phaseswitchassessment', 0, ['id' => $workshop->id]);
            }
            mtrace('done');
        }
    }
}
