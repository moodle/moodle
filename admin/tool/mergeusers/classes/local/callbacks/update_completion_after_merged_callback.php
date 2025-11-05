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
 * Callback to update user's completion for the merged user to keep.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\callbacks;

use coding_exception;
use dml_exception;
use tool_mergeusers\hook\after_merged_all_tables;

/**
 * Callback that updates the user's completion for the user to keep.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_completion_after_merged_callback {
    /**
     * Updates the course_completions.reaggretate field with the current time.
     *
     * This makes Moodle core updating course completions for the user to keep.
     * Moodle internals updates the course's completion status in short.
     *
     * This version of the course completion is inspired by the completionlib_test.php::test_aggregate_completions().
     *
     * @param after_merged_all_tables $hook
     * @return void
     * @throws dml_exception
     * @throws coding_exception
     */
    public static function update_completion(after_merged_all_tables $hook): void {
        global $CFG, $DB;
        require_once($CFG->libdir . '/completionlib.php');

        $now = time() - 2; // MDL-33320: for instant completions we need aggregate to work in a single run.
        $params = [
            'toid' => $hook->toid,
            'notime' => 0,
        ];
        $courseids = $DB->get_fieldset_sql(
            'SELECT course
                  FROM {course_completions}
                 WHERE userid = :toid
                  AND (timecompleted IS NULL OR timecompleted = :notime)',
            $params,
        );
        $ncourses = count($courseids);
        if ($ncourses <= 0) {
            $hook->add_log('Course completion reaggregation asked for no courses.');
            return;
        }
        // Look for courses to reaggregate course completion.
        $cc = [
            'userid' => $hook->toid,
        ];
        foreach ($courseids as $courseid) {
            $cc['course'] = $courseid;
            $ccompletion = new \completion_completion($cc);
            $completion = $ccompletion->mark_inprogress($now);
            // Just aggregate this course completion for this user.
            aggregate_completions($completion);
        }
        // With this version of the updating process, the course completion reaggregation is included into the merge process.
        $hook->add_log(sprintf(
            'Course completion reaggregated for user %d and these %d courses: %s.',
            $hook->toid,
            $ncourses,
            implode(',', $courseids),
        ));
    }
}
