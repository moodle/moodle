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
 * Event observers for workshopallocation_scheduled.
 *
 * @package workshopallocation_scheduled
 * @copyright 2013 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace workshopallocation_scheduled;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for workshopallocation_scheduled observers.
 *
 * @package workshopallocation_scheduled
 * @copyright 2013 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Triggered when the '\mod_workshop\event\course_module_viewed' event is triggered.
     *
     * This does the same job as {@link workshopallocation_scheduled_cron()} but for the
     * single workshop. The idea is that we do not need to wait for cron to execute.
     * Displaying the workshop main view.php can trigger the scheduled allocation, too.
     *
     * @param \mod_workshop\event\course_module_viewed $event
     * @return bool
     */
    public static function workshop_viewed($event) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/mod/workshop/locallib.php');

        $workshop = $event->get_record_snapshot('workshop', $event->objectid);
        $course   = $event->get_record_snapshot('course', $event->courseid);
        $cm       = $event->get_record_snapshot('course_modules', $event->contextinstanceid);

        $workshop = new \workshop($workshop, $cm, $course);
        $now = time();

        // Non-expensive check to see if the scheduled allocation can even happen.
        if ($workshop->phase == \workshop::PHASE_SUBMISSION and $workshop->submissionend > 0 and $workshop->submissionend < $now) {

            // Make sure the scheduled allocation has been configured for this workshop, that it has not
            // been executed yet and that the passed workshop record is still valid.
            $sql = "SELECT a.id
                      FROM {workshopallocation_scheduled} a
                      JOIN {workshop} w ON a.workshopid = w.id
                     WHERE w.id = :workshopid
                           AND a.enabled = 1
                           AND w.phase = :phase
                           AND w.submissionend > 0
                           AND w.submissionend < :now
                           AND (a.timeallocated IS NULL OR a.timeallocated < w.submissionend)";
            $params = array('workshopid' => $workshop->id, 'phase' => \workshop::PHASE_SUBMISSION, 'now' => $now);

            if ($DB->record_exists_sql($sql, $params)) {
                // Allocate submissions for assessments.
                $allocator = $workshop->allocator_instance('scheduled');
                $result = $allocator->execute();
                // Todo inform the teachers about the results.
            }
        }
        return true;
    }

    /**
     * Called when the '\mod_workshop\event\phase_automatically_switched' event is triggered.
     *
     * This observer handles the phase_automatically_switched event triggered when phaseswithassesment is active
     * and the phase is automatically switched.
     *
     * When this happens, this situation can occur:
     *
     *     * cron_task transition the workshop to PHASE_ASESSMENT.
     *     * scheduled_allocator task executes.
     *     * scheduled_allocator task cannot allocate parcipants because workshop is not
     *       in PHASE_SUBMISSION state (it's in PHASE_ASSESMENT).
     *
     * @param \mod_workshop\event\phase_automatically_switched $event
     */
    public static function phase_automatically_switched(\mod_workshop\event\phase_automatically_switched $event) {
        if ($event->other['previousworkshopphase'] != \workshop::PHASE_SUBMISSION) {
            return;
        }
        if ($event->other['targetworkshopphase'] != \workshop::PHASE_ASSESSMENT) {
            return;
        }

        $workshop = $event->get_record_snapshot('workshop', $event->objectid);
        $course   = $event->get_record_snapshot('course', $event->courseid);
        $cm       = $event->get_record_snapshot('course_modules', $event->contextinstanceid);

        $workshop = new \workshop($workshop, $cm, $course);
        if ($workshop->phase != \workshop::PHASE_ASSESSMENT) {
            return;
        }

        $allocator = $workshop->allocator_instance('scheduled');
        // We know that we come from PHASE_SUBMISSION so we tell the allocator not to test for the PHASE_SUBMISSION state.
        $allocator->execute(false);
    }
}
