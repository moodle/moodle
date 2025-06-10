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

namespace tool_migratehvp2h5p;

use tool_migratehvp2h5p\api;
use advanced_testcase;
use stdClass;
use stored_file;

/**
 * Tests for determining the author of the HVP instances when importing to H5Pactivity
 *
 * @package    tool_migratehvp2h5p
 * @category   test
 * @copyright  2021 Jonathan Harker <jonathan@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_migratehvp2h5p\api
 */
class author_test extends advanced_testcase {

    /**
     * @var int The HVP module id.
     */
    protected $modid;

    /**
     * Create a fake mod_hvp instance and assign it to a course.
     * TODO: Can't currently rely on a HVP generator as there isn't one (yet).
     *
     * @param stdClass $course a Moodle course object.
     * @return stdClass A faked mod_hvp object.
     */
    private function fake_hvp(stdClass $course): stdClass {
        global $DB;

        // Check that mod_hvp activity type is installed, save its id.
        if (empty($this->modid)) {
            $mod = $DB->get_record('modules', [ 'name' => 'hvp' ], '*', IGNORE_MISSING);
            if (empty($mod)) {
                $this->fail("The 'mod_hvp' plugin must be installed for these tests to succeed.");
            }
            $this->modid = $mod->id;
        }

        // Store a HVP instance.
        $now = time();
        $hvp = (object) [
            'course'            => $course->id,
            'name'              => 'Test HVP',
            'slug'              => 'test-hvp',
            'intro'             => 'Intro text for Test HVP',
            'introformat'       => 1,
            'json_content'      => '{}',
            'main_library_id'   => 0,
            'timecreated'       => $now,
            'timemodified'      => $now,
        ];
        $id = $DB->insert_record('hvp', $hvp);
        $hvp = $DB->get_record('hvp', [ 'id' => $id ], '*', MUST_EXIST);

        // Minimally add it to the course.
        $cm = (object) [
            'course'    => $course->id,
            'module'    => $this->modid,
            'instance'  => $hvp->id,
            'added'     => $now,
        ];
        $cm->id = $DB->insert_record('course_modules', $cm);
        $hvp->cm = $cm;

        // Fake a minimal viable module context.
        $context = (object) [
            'contextlevel' => CONTEXT_MODULE,
            'instanceid'   => $hvp->cm->id,
            'depth'        => 0,
            'path'         => null,
            'locked'       => 0,
        ];
        $context->id = $DB->insert_record('context', $context);
        $hvp->context = $context;

        return $hvp;
    }

    /**
     * Associate a file to an HVP activity.
     *
     * @param stdClass $hvp The HVP activity.
     * @param string $filename The name of the file.
     * @param int $userid The user associated with the file.
     * @param string $content The file content. Defaults to 'hello' if not specified.
     * @return stored_file a Moodle file record.
     */
    private function fake_file(stdClass $hvp, string $filename, int $userid, string $content = 'hello'): stored_file {
        $filerecord = [
            'filename'  => $filename,
            'filepath'  => '/',
            'filearea'  => 'content',
            'component' => 'mod_hvp',
            'itemid'    => $hvp->id,
            'contextid' => $hvp->context->id,
            'userid'    => $userid,
        ];
        $fs = get_file_storage();
        return $fs->create_file_from_string($filerecord, $content);
    }

    /**
     * Fake a log entry for adding a course module.
     *
     * @param stdClass $hvp The HVP activity.
     * @param int $userid The user associated with the file.
     * @return stdClass a Moodle log record.
     */
    private function fake_log(stdClass $hvp, int $userid): stdClass {
        global $DB;
        $log = (object) [
            'timecreated' => time(),
            'eventname' => '\core\event\course_module_created',
            'edulevel' => 0,
            'component' => 'core',
            'target'    => 'course_module',
            'action'    => 'created',
            'courseid'  => $hvp->course,
            'objectid'  => $hvp->cm->id,
            'userid' => $userid,
            'contextid' => $hvp->context->id,
            'contextlevel' => $hvp->context->contextlevel,
            'contextinstanceid' => $hvp->context->instanceid,
        ];
        $log->id = $DB->insert_record('logstore_standard_log', $log);
        return $log;
    }

    /**
     * Set up access to the log store.
     * @return void
     */
    private function setup_logs() {
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        get_log_manager(true);
    }

    /**
     * Log entry exists; HVP has no files; course has enrolled editing teachers.
     *
     * @covers ::get_hvp_author
     * @return void
     */
    public function test_log_user(): void {
        $this->setup_logs();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $teacher = $dg->create_user([ 'username' => 'teacher1' ]);
        $loguser = $dg->create_user([ 'username' => 'loguser' ]);
        $dg->enrol_user($teacher->id, $course->id, 'editingteacher');
        $hvp = $this->fake_hvp($course);
        $log = $this->fake_log($hvp, $loguser->id);
        $this->setAdminUser();

        // Test.
        $author = api::get_hvp_author($hvp);

        // We expect everything to fall through to the current (admin) user.
        $this->assertTrue($author == $loguser->id);
    }

    /**
     *  No log entry; HVP has no files; course has no enrolled teachers.
     *
     * @covers ::get_hvp_author
     * @return void
     */
    public function test_tool_runner(): void {
        $this->setup_logs();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $student = $dg->create_user([ 'username' => 'student1' ]);
        $dg->enrol_user($student->id, $course->id, 'student');
        $hvp = $this->fake_hvp($course);
        $this->setAdminUser();

        // Test.
        $author = api::get_hvp_author($hvp);

        // We expect everything to fall through to the current (admin) user.
        $admin = \core_user::get_user_by_username('admin');
        $this->assertEquals($admin->id, $author);
    }

    /**
     * No log entry; HVP has a file, but owner is not an editing teacher.
     *
     * @covers ::get_hvp_author
     * @return void
     */
    public function test_nonediting_file_user(): void {
        $this->setup_logs();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $teacher = $dg->create_user([ 'username' => 'teacher' ]);
        $fileuser = $dg->create_user([ 'username' => 'fileuser' ]);
        $dg->enrol_user($teacher->id, $course->id, 'editingteacher');
        $dg->enrol_user($fileuser->id, $course->id, 'teacher');
        $hvp = $this->fake_hvp($course);
        $this->fake_file($hvp, 'hello.txt', $fileuser->id);
        $this->setAdminUser();

        // Test.
        $author = api::get_hvp_author($hvp);

        // We expect the first editing teacher, since file owner is not an editing teacher.
        $this->assertTrue($author == $teacher->id);
    }

    /**
     * No log entry; HVP has a file, owner is an editing teacher.
     *
     * @covers ::get_hvp_author
     * @return void
     */
    public function test_editing_file_user(): void {
        $this->setup_logs();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $teacher = $dg->create_user([ 'username' => 'teacher' ]);
        $fileuser = $dg->create_user([ 'username' => 'fileuser' ]);
        $dg->enrol_user($teacher->id, $course->id, 'editingteacher');
        $dg->enrol_user($fileuser->id, $course->id, 'editingteacher');
        $hvp = $this->fake_hvp($course);
        $this->fake_file($hvp, 'hello.txt', $fileuser->id);
        $this->setAdminUser();

        // Test.
        $author = api::get_hvp_author($hvp);

        // We expect the file owner, since they are an editing teacher.
        $this->assertTrue($author == $fileuser->id);
    }

    /**
     * No log entry; HVP has no files; course has an enrolled teacher.
     *
     * @covers ::get_hvp_author
     * @return void
     */
    public function test_first_editingteacher_assigned(): void {
        $this->setup_logs();
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();

        $course = $dg->create_course();
        $teacher = $dg->create_user([ 'username' => 'teacher1' ]);
        $dg->enrol_user($teacher->id, $course->id, 'editingteacher');
        $hvp = $this->fake_hvp($course);
        $this->setAdminUser();

        // Test.
        $author = api::get_hvp_author($hvp);

        // We expect the editing teacher.
        $this->assertTrue($author == $teacher->id);
    }
}
