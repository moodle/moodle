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
 * Automated backup tests.
 *
 * @package    core_backup
 * @copyright  2019 John Yao <johnyao@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');
require_once($CFG->libdir.'/cronlib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Automated backup tests.
 *
 * @copyright  2019 John Yao <johnyao@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_automated_backup_testcase extends advanced_testcase {
    /**
     * @var \backup_cron_automated_helper
     */
    protected $backupcronautomatedhelper;

    /**
     * @var stdClass $course
     */
    protected $course;

    protected function setUp(): void {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $CFG->enablecompletion = true;

        // Getting a testable backup_cron_automated_helper class.
        $this->backupcronautomatedhelper = new test_backup_cron_automated_helper();

        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course(
                array('format' => 'topics', 'numsections' => 3,
                        'enablecompletion' => COMPLETION_ENABLED),
                array('createsections' => true));
        $forum = $generator->create_module('forum', array(
                'course' => $this->course->id));
        $forum2 = $generator->create_module('forum', array(
                'course' => $this->course->id, 'completion' => COMPLETION_TRACKING_MANUAL));

        // We need a grade, easiest is to add an assignment.
        $assignrow = $generator->create_module('assign', array(
                'course' => $this->course->id));
        $assign = new assign(context_module::instance($assignrow->cmid), false, false);
        $item = $assign->get_grade_item();

        // Make a test grouping as well.
        $grouping = $generator->create_grouping(array('courseid' => $this->course->id,
                'name' => 'Grouping!'));

        $availability = '{"op":"|","show":false,"c":[' .
                '{"type":"completion","cm":' . $forum2->cmid .',"e":1},' .
                '{"type":"grade","id":' . $item->id . ',"min":4,"max":94},' .
                '{"type":"grouping","id":' . $grouping->id . '}' .
                ']}';
        $DB->set_field('course_modules', 'availability', $availability, array(
                'id' => $forum->cmid));
        $DB->set_field('course_sections', 'availability', $availability, array(
                'course' => $this->course->id, 'section' => 1));
    }

    /**
     * Tests the automated backup run when the there is course backup should be skipped.
     */
    public function test_automated_backup_skipped_run() {
        global $DB;

        // Enable automated back up.
        set_config('backup_auto_active', true, 'backup');
        set_config('backup_auto_weekdays', '1111111', 'backup');

        // Start backup process.
        $admin = get_admin();

        // Backup entry should not exist.
        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));
        $this->assertFalse($backupcourse);
        $this->assertInstanceOf(
            backup_cron_automated_helper::class,
            $this->backupcronautomatedhelper->return_this()
        );

        $classobject = $this->backupcronautomatedhelper->return_this();

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'get_courses');
        $method->setAccessible(true); // Allow accessing of private method.
        $courses = $method->invoke($classobject);

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'check_and_push_automated_backups');
        $method->setAccessible(true); // Allow accessing of private method.
        $emailpending = $method->invokeArgs($classobject, [$courses, $admin]);

        $coursename = $this->course->fullname;
        $this->expectOutputRegex("/Skipping $coursename \(Not scheduled for backup until/");
        $this->assertFalse($emailpending);

        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));
        $this->assertNotNull($backupcourse->laststatus);
    }

    /**
     * Tests the automated backup run when the there is course backup can be pushed to adhoc task.
     */
    public function test_automated_backup_push_run() {
        global $DB;

        // Enable automated back up.
        set_config('backup_auto_active', true, 'backup');
        set_config('backup_auto_weekdays', '1111111', 'backup');

        $admin = get_admin();

        $classobject = $this->backupcronautomatedhelper->return_this();

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'get_courses');
        $method->setAccessible(true); // Allow accessing of private method.
        $courses = $method->invoke($classobject);

        // Create this backup course.
        $backupcourse = new stdClass;
        $backupcourse->courseid = $this->course->id;
        $backupcourse->laststatus = backup_cron_automated_helper::BACKUP_STATUS_NOTYETRUN;
        $DB->insert_record('backup_courses', $backupcourse);
        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));

        // We now manually trigger a backup pushed to adhoc task.
        // Make sure is in the past, which means should run now.
        $backupcourse->nextstarttime = time() - 10;
        $DB->update_record('backup_courses', $backupcourse);

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'check_and_push_automated_backups');
        $method->setAccessible(true); // Allow accessing of private method.
        $emailpending = $method->invokeArgs($classobject, [$courses, $admin]);
        $this->assertTrue($emailpending);

        $coursename = $this->course->fullname;
        $this->expectOutputRegex("/Putting backup of $coursename in adhoc task queue/");

        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));
        // Now this backup course status should be queued.
        $this->assertEquals(backup_cron_automated_helper::BACKUP_STATUS_QUEUED, $backupcourse->laststatus);
    }

    /**
     * Tests the automated backup inactive run.
     */
    public function test_inactive_run() {
        backup_cron_automated_helper::run_automated_backup();
        $this->expectOutputString("Checking automated backup status...INACTIVE\n");
    }

    /**
     * Tests the invisible course being skipped.
     */
    public function test_should_skip_invisible_course() {
        global $DB;

        set_config('backup_auto_active', true, 'backup');
        set_config('backup_auto_skip_hidden', true, 'backup');
        set_config('backup_auto_weekdays', '1111111', 'backup');
        // Create this backup course.
        $backupcourse = new stdClass;
        $backupcourse->courseid = $this->course->id;
        // This is the status we believe last run was OK.
        $backupcourse->laststatus = backup_cron_automated_helper::BACKUP_STATUS_SKIPPED;
        $DB->insert_record('backup_courses', $backupcourse);
        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));

        $this->assertTrue(course_change_visibility($this->course->id, false));
        $course = $DB->get_record('course', array('id' => $this->course->id));
        $this->assertEquals('0', $course->visible);
        $classobject = $this->backupcronautomatedhelper->return_this();
        $nextstarttime = backup_cron_automated_helper::calculate_next_automated_backup(null, time());

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'should_skip_course_backup');
        $method->setAccessible(true); // Allow accessing of private method.
        $skipped = $method->invokeArgs($classobject, [$backupcourse, $course, $nextstarttime]);

        $this->assertTrue($skipped);
        $this->expectOutputRegex("/Skipping $course->fullname \(Not visible\)/");
    }

    /**
     * Tests the not modified course being skipped.
     */
    public function test_should_skip_not_modified_course_in_days() {
        global $DB;

        set_config('backup_auto_active', true, 'backup');
        // Skip if not modified in two days.
        set_config('backup_auto_skip_modif_days', 2, 'backup');
        set_config('backup_auto_weekdays', '1111111', 'backup');

        // Create this backup course.
        $backupcourse = new stdClass;
        $backupcourse->courseid = $this->course->id;
        // This is the status we believe last run was OK.
        $backupcourse->laststatus = backup_cron_automated_helper::BACKUP_STATUS_SKIPPED;
        $backupcourse->laststarttime = time() - 2 * DAYSECS;
        $backupcourse->lastendtime = time() - 1 * DAYSECS;
        $DB->insert_record('backup_courses', $backupcourse);
        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));
        $course = $DB->get_record('course', array('id' => $this->course->id));

        $course->timemodified = time() - 2 * DAYSECS - 1;

        $classobject = $this->backupcronautomatedhelper->return_this();
        $nextstarttime = backup_cron_automated_helper::calculate_next_automated_backup(null, time());

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'should_skip_course_backup');
        $method->setAccessible(true); // Allow accessing of private method.
        $skipped = $method->invokeArgs($classobject, [$backupcourse, $course, $nextstarttime]);

        $this->assertTrue($skipped);
        $this->expectOutputRegex("/Skipping $course->fullname \(Not modified in the past 2 days\)/");
    }

    /**
     * Tests the backup not modified course being skipped.
     */
    public function test_should_skip_not_modified_course_since_prev() {
        global $DB;

        set_config('backup_auto_active', true, 'backup');
        // Skip if not modified in two days.
        set_config('backup_auto_skip_modif_prev', 2, 'backup');
        set_config('backup_auto_weekdays', '1111111', 'backup');

        // Create this backup course.
        $backupcourse = new stdClass;
        $backupcourse->courseid = $this->course->id;
        // This is the status we believe last run was OK.
        $backupcourse->laststatus = backup_cron_automated_helper::BACKUP_STATUS_SKIPPED;
        $backupcourse->laststarttime = time() - 2 * DAYSECS;
        $backupcourse->lastendtime = time() - 1 * DAYSECS;
        $DB->insert_record('backup_courses', $backupcourse);
        $backupcourse = $DB->get_record('backup_courses', array('courseid' => $this->course->id));
        $course = $DB->get_record('course', array('id' => $this->course->id));

        $course->timemodified = time() - 2 * DAYSECS - 1;

        $classobject = $this->backupcronautomatedhelper->return_this();
        $nextstarttime = backup_cron_automated_helper::calculate_next_automated_backup(null, time());

        $method = new ReflectionMethod('\backup_cron_automated_helper', 'should_skip_course_backup');
        $method->setAccessible(true); // Allow accessing of private method.
        $skipped = $method->invokeArgs($classobject, [$backupcourse, $course, $nextstarttime]);

        $this->assertTrue($skipped);
        $this->expectOutputRegex("/Skipping $course->fullname \(Not modified since previous backup\)/");
    }
}

/**
 * New backup_cron_automated_helper class for testing.
 *
 * This class extends the helper backup_cron_automated_helper class
 * in order to utilise abstract class for testing.
 *
 * @package    core
 * @copyright  2019 John Yao <johnyao@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_backup_cron_automated_helper extends backup_cron_automated_helper {
    /**
     * Returning this for testing.
     */
    public function return_this() {
        return $this;
    }
}
