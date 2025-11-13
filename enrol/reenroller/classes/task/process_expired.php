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
 * @package    enrol_reenroller
 * @copyright  2025 Onwards LSU Online & Continuing Education
 * @author     2025 Onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_reenroller\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use moodle_exception;

class process_expired extends scheduled_task {
    /**
     * Get the configured timeline in seconds.
     *
     * @return int Seconds representation of the configured timeline.
     */
    public static function get_timeline_seconds(): int {
        $value = (int)get_config('enrol_reenroller', 'timelinevalue');
        $unit  = get_config('enrol_reenroller', 'timelineunit');

        return match ($unit) {
            'days'   => $value * DAYSECS,
            'weeks'  => $value * WEEKSECS,
            'months' => $value * 2629746,
            'years'  => $value * YEARSECS,
            default  => 0,
        };
    }

    public function get_name(): string {
        return get_string('task:processexpired', 'enrol_reenroller');
    }

    public function execute() {
        global $DB;

        // Read plugin settings.
        $catsetting = get_config('enrol_reenroller', 'targetcategory');
        $sourcerole = (int)get_config('enrol_reenroller', 'sourcerole');
        $targetrole = (int)get_config('enrol_reenroller', 'targetrole');
        $instancename = get_config('enrol_reenroller', 'instance_name') ?: 'd1';
        $startdate = get_config('enrol_reenroller', 'startdate') ?: time();

        // Make sure the config is set.
        if (empty($catsetting) || $targetrole <= 0 || $sourcerole <= 0) {

            // Nothing to do if not configured.
            mtrace('enrol_reenroller: target category, source role, or target role not configured; skipping.');
            return;
        }

        // Instantiate the plugin.
        $plugin = enrol_get_plugin('reenroller');
        if (!$plugin) {
            mtrace('enrol_reenroller: plugin not found; skipping.');
            return;
        }

        // Build an array of these.
        $categoryids = array_filter(explode(',', $catsetting));

        // Build the insql.
        list($insql, $parms) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);

        // Add this parm.
        $parms['d1plugin'] = $instancename;
        $parms['startdate'] = $startdate;

        // Find user_enrolments that meet criteria.
        $sql = "
            SELECT ue.*, e.id AS enrolinstanceid, e.courseid, e.enrol AS enrolplugin
            FROM {user_enrolments} ue
            JOIN {enrol} e ON e.id = ue.enrolid
            JOIN {course} c ON c.id = e.courseid
            WHERE c.visible = 1
              AND e.enrol = :d1plugin
              AND ue.timestart > 0
              AND ue.timestart < UNIX_TIMESTAMP()
              AND ue.timeend > :startdate
              AND ue.timeend < UNIX_TIMESTAMP()
              AND ue.status = 0
              $insql";

        // Get the expired users.
        $expired = $DB->get_records_sql($sql, $parms);

        // If we don't have any, don't do anything.
        if (empty($expired)) {
            mtrace('enrol_reenroller: no matching expired d1 enrollments found.');
            return;
        }

        // For each expired enrolment, verify completion then migrate.
        foreach ($expired as $ue) {
            try {

                // Check if the user has already been reenrolled.
                $reenroller = $DB->get_records(
                    'enrol',
                    ['courseid' => $ue->courseid, 'enrol' => 'reenroller']
                );

                // Set these for later.
                $completed = false;

                // If we have a populated array.
                if ($reenroller && !empty($reenroller)) {

                    foreach($reenroller as $re) {

                        // Check to see if they are already enrolled.
                        $reenrolled = $DB->record_exists(
                            'user_enrolments',
                            ['userid' => $ue->userid, 'enrolid' => $re->id]
                        );

                        if ($reenrolled) {
                            mtrace("enrol_reenroller: user {$ue->userid} already reenrolled in course {$ue->courseid}; skipping.");
                            continue 2;
                        }
                    }
                }

                // Confirm the user actually completed the course.
                $completed = $DB->record_exists_select(
                    'course_completions',
                    'userid = :userid AND course = :courseid AND timecompleted > 0',
                    ['userid' => $ue->userid, 'courseid' => $ue->courseid]
                );

                if (!$completed) {

                    // Skip if not completed.
                    mtrace("enrol_reenroller: user {$ue->userid} has not completed course {$ue->courseid}; skipping.");
                    continue;
                }

                // Get or create an instance of this plugin in the course.
                $instances = enrol_get_instances($ue->courseid, true);
                $ourinstance = null;
                foreach ($instances as $inst) {
                    if ($inst->enrol === 'reenroller') {
                        $ourinstance = $inst;
                        break;
                    }
                }

                if (!$ourinstance) {

                    // Create a new instance with default settings.
                    $fields = (object)[
                        'status' => 0,
                        'name' => 'reenroller'
                    ];

                    // Add the new instance to the course.
                    $ourinstance = $plugin->add_instance($DB->get_record('course', ['id' => $ue->courseid]), (array)$fields);

                    // We need the full instance object afterwards.
                    if (is_int($ourinstance)) {
                        $ourinstance = $DB->get_record('enrol', ['id' => $ourinstance]);
                    } else {

                        // Ensure the enrollment instance is real.
                        $ourinstance = $DB->get_record('enrol', ['courseid' => $ue->courseid, 'enrol' => 'reenroller'], '*', MUST_EXIST);
                    }
                }

                // Get some seconds.
                $seconds = self::get_timeline_seconds();

                // Start now, expire in $seconds.
                $timestart = time();
                $timeend = $timestart + $seconds;

                // Enrol the user into our instance, with timeend set, and target role.
                $plugin->enrol_user($ourinstance, $ue->userid, $targetrole, $timestart, $timeend);

                // Suspend the old user_enrolment to preserve audit trail.
                // $DB->set_field('user_enrolments', 'status', 1, ['id' => $ue->id]);

                // Remove role_assignments created by the d1 enrolment for the course context.
                $context = \context_course::instance($ue->courseid);

                $DB->delete_records_select('role_assignments',
                    " userid = :uid AND contextid = :ctxid AND component = :comp AND itemid = :itemid",
                    [
                        'uid' => $ue->userid,
                        'ctxid' => $context->id,
                        'comp' => 'enrol_d1',
                        'itemid' => $ue->id
                    ]
                );

                mtrace("Successfully migrated user {$ue->userid} in course {$ue->courseid}");
            } catch (\Exception $ex) {
                mtrace("Error processing ue id {$ue->id} - " . $ex->getMessage());
            }
        }
    }
}
