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
 * mod_h5pactivity generator tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity;

use advanced_testcase;
use backup;
use backup_controller;
use backup_setting;
use restore_controller;
use restore_dbops;
use stdClass;

/**
 * Genarator tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    }

    /**
     * Test on H5P activity backup and restore.
     *
     * @dataProvider backup_restore_data
     * @param bool $content if has to create attempts
     * @param bool $userdata if backup have userdata
     * @param array $result1 data to check on original course
     * @param array $result2 data to check on resotred course
     */
    public function test_backup_restore(bool $content, bool $userdata, array $result1, array $result2): void {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create one activity.
        $this->assertFalse($DB->record_exists('h5pactivity', ['course' => $course->id]));
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $cm = get_coursemodule_from_id('h5pactivity', $activity->cmid, 0, false, MUST_EXIST);

        if ($content) {
            $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');
            $params = ['cmid' => $cm->id, 'userid' => $user->id];
            $generator->create_content($activity, $params);
        }

        $this->assertEquals($result1[0], $DB->count_records('h5pactivity', ['course' => $course->id]));
        $this->assertEquals($result1[1], $DB->count_records('h5pactivity_attempts', ['h5pactivityid' => $activity->id]));
        $attemptid = $DB->get_field('h5pactivity_attempts', 'id', ['h5pactivityid' => $activity->id]);
        $this->assertEquals($result1[2], $DB->count_records('h5pactivity_attempts_results', ['attemptid' => $attemptid]));

        // Execute course backup and restore.
        $newcourseid = $this->backup_and_restore($course, $userdata);

        // Check original activity.
        $this->assertEquals($result1[0], $DB->count_records('h5pactivity', ['course' => $course->id]));
        $this->assertEquals($result1[1], $DB->count_records('h5pactivity_attempts', ['h5pactivityid' => $activity->id]));
        $attempt = $DB->get_record('h5pactivity_attempts', ['h5pactivityid' => $activity->id]);
        $attemptid = $attempt->id ?? 0;
        $this->assertEquals($result1[2], $DB->count_records('h5pactivity_attempts_results', ['attemptid' => $attemptid]));

        // Check original activity.
        $this->assertEquals($result2[0], $DB->count_records('h5pactivity', ['course' => $newcourseid]));
        $activity2 = $DB->get_record('h5pactivity', ['course' => $newcourseid]);
        $this->assertEquals($result2[1], $DB->count_records('h5pactivity_attempts', ['h5pactivityid' => $activity2->id]));
        $attempt2 = $DB->get_record('h5pactivity_attempts', ['h5pactivityid' => $activity2->id]);
        $attempt2id = $attempt2->id ?? 0;
        $this->assertEquals($result2[2], $DB->count_records('h5pactivity_attempts_results', ['attemptid' => $attempt2id]));

        // Compare activities.
        $this->assertEquals($newcourseid, $activity2->course);
        $this->assertEquals($activity->name, $activity2->name);
        $this->assertEquals($activity->intro, $activity2->intro);
        $this->assertEquals($activity->introformat, $activity2->introformat);
        $this->assertEquals($activity->grade, $activity2->grade);
        $this->assertEquals($activity->displayoptions, $activity2->displayoptions);
        $this->assertEquals($activity->enabletracking, $activity2->enabletracking);
        $this->assertEquals($activity->grademethod, $activity2->grademethod);

        // Compare attempts.
        if ($content && $userdata) {
            $this->assertEquals($activity2->id, $attempt2->h5pactivityid);
            $this->assertEquals($attempt->userid, $attempt2->userid);
            $this->assertEquals($attempt->timecreated, $attempt2->timecreated);
            $this->assertEquals($attempt->timemodified, $attempt2->timemodified);
            $this->assertEquals($attempt->attempt, $attempt2->attempt);
            $this->assertEquals($attempt->rawscore, $attempt2->rawscore);
            $this->assertEquals($attempt->maxscore, $attempt2->maxscore);
            $this->assertEquals($attempt->duration, $attempt2->duration);
            $this->assertEquals($attempt->completion, $attempt2->completion);
            $this->assertEquals($attempt->success, $attempt2->success);

            // Compare results.
            $results = $DB->get_records('h5pactivity_attempts_results', ['attemptid' => $attempt->id]);
            foreach ($results as $result) {
                $result2 = $DB->get_record('h5pactivity_attempts_results', [
                    'subcontent' => $result->subcontent, 'attemptid' => $attempt2->id
                ]);
                $this->assertNotFalse($result2);
                $this->assertEquals($result->timecreated, $result2->timecreated);
                $this->assertEquals($result->interactiontype, $result2->interactiontype);
                $this->assertEquals($result->description, $result2->description);
                $this->assertEquals($result->correctpattern, $result2->correctpattern);
                $this->assertEquals($result->response, $result2->response);
                $this->assertEquals($result->additionals, $result2->additionals);
                $this->assertEquals($result->rawscore, $result2->rawscore);
                $this->assertEquals($result->maxscore, $result2->maxscore);
                $this->assertEquals($result->duration, $result2->duration);
                $this->assertEquals($result->completion, $result2->completion);
                $this->assertEquals($result->success, $result2->success);
            }
        }

    }

    /**
     * Data provider for test_backup_restore.
     *
     * @return array
     */
    public static function backup_restore_data(): array {
        return [
            'Activity attempts and restore with userdata' => [
                true, true, [1, 1, 3], [1, 1, 3]
            ],
            'No activity attempts and restore with userdata' => [
                false, true, [1, 0, 0], [1, 0, 0]
            ],
            'Activity attempts and restore with no userdata' => [
                true, false, [1, 1, 3], [1, 0, 0]
            ],
            'No activity attempts and restore with no userdata' => [
                false, false, [1, 0, 0], [1, 0, 0]
            ],
        ];
    }

    /**
     * Backs a course up and restores it.
     *
     * @param stdClass $srccourse Course object to backup
     * @param bool $userdata if the backup must be with user data
     * @return int ID of newly restored course
     */
    private function backup_and_restore(stdClass $srccourse, bool $userdata): int {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $srccourse->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);

        $bc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $bc->get_plan()->get_setting('users')->set_value($userdata);

        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course(
            $srccourse->fullname, $srccourse->shortname . '_2', $srccourse->category
        );
        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);

        $rc->get_plan()->get_setting('users')->set_status(backup_setting::NOT_LOCKED);
        $rc->get_plan()->get_setting('users')->set_value($userdata);

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }
}
