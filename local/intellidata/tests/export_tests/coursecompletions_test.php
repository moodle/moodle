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
 * Course completions migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use completion_completion;
use context_course;
use local_intellidata\custom_db_client_testcase;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Course completions migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class coursecompletions_test extends custom_db_client_testcase {

    /**
     * Test course completions create.
     *
     * @covers \local_intellidata\entities\coursecompletions\coursecompletion
     * @covers \local_intellidata\entities\coursecompletions\migration
     * @covers \local_intellidata\entities\coursecompletions\observer::course_completed
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_course_completions_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_course_completions_test(0);
    }

    /**
     * Test course completions update.
     *
     * @covers \local_intellidata\entities\coursecompletions\coursecompletion
     * @covers \local_intellidata\entities\coursecompletions\migration
     * @covers \local_intellidata\entities\coursecompletions\observer::course_completion_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_course_completions_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_course_completions_test(0);
    }

    /**
     * Course completions create test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function create_course_completions_test($tracking) {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $data = [
            'firstname' => 'aibuser1' . $tracking,
            'username' => 'aibuser1' . $tracking,
        ];

        $userdata = $data;
        $userdata['password'] = 'Ibuser1!';

        if (!$user = $DB->get_record('user', $data)) {
            $user = generator::create_user($userdata);
        }

        $coursedata = [
            'fullname' => 'ibcoursecompletion1' . $tracking,
            'idnumber' => 'a1111111' . $tracking,
            'shortname' => 'ibcoursecompletion1' . $tracking,
            'enablecompletion' => true,
        ];

        if (!$course = $DB->get_record('course', $coursedata)) {
            $course = generator::create_course($coursedata);
        }

        $data = [
            'userid' => $user->id,
            'course' => $course->id,
        ];

        // Create coursecompletion.
        $coursecompletion = new completion_completion($data);
        $coursecompletion->mark_complete(time());

        unset($data['course']);
        $data['courseid'] = $course->id;

        $entity = new \local_intellidata\entities\coursecompletions\coursecompletion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'coursecompletions']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Course completions update test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function update_course_completions_test($tracking) {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        $coursedata = [
            'fullname' => 'ibcoursecompletion1' . $tracking,
            'idnumber' => 'a1111111' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);

        $data = [
            'courseid' => $course->id,
            'context' => context_course::instance($course->id),
        ];

        \core\event\course_completion_updated::create($data)->trigger();

        unset($data['context']);

        $entity = new \local_intellidata\entities\coursecompletions\coursecompletion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'coursecompletions']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
