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
 * Course migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Course migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class courses_test extends custom_db_client_testcase {

    /**
     * Test course create.
     *
     * @covers \local_intellidata\entities\courses\course
     * @covers \local_intellidata\entities\courses\migration
     * @covers \local_intellidata\entities\courses\observer::course_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_course_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_course_test(0);
    }

    /**
     * Test course update.
     *
     * @covers \local_intellidata\entities\courses\course
     * @covers \local_intellidata\entities\courses\migration
     * @covers \local_intellidata\entities\courses\observer::course_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_course_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_course_test(0);
    }

    /**
     * Test course delete.
     *
     * @covers \local_intellidata\entities\courses\course
     * @covers \local_intellidata\entities\courses\migration
     * @covers \local_intellidata\entities\courses\observer::course_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_course_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_course_test(0);
    }

    /**
     * Delete course test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_course_test($tracking) {
        global $DB;

        $data = [
            'fullname' => 'cibcourse1' . $tracking,
        ];

        $course = $DB->get_record('course', $data);

        delete_course($course, false);

        $entity = new \local_intellidata\entities\courses\course($course);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'courses']);

        $datarecord = $storage->get_log_entity_data('d', ['id' => $course->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = json_decode($datarecord->data);

        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update course test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_course_test($tracking) {
        global $DB;

        $data = [
            'fullname' => 'cibcourse1' . $tracking,
        ];

        $course = $DB->get_record('course', $data);
        $course->idnumber = '2222222' . $tracking;
        $data['idnumber'] = $course->idnumber;

        update_course($course);

        $entity = new \local_intellidata\entities\courses\course($course);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'courses']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $course->id, 'idnumber' => $course->idnumber]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create course test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function create_course_test($tracking) {
        global $DB;
        $data = [
            'fullname' => 'cibcourse1' . $tracking,
            'idnumber' => 'c1111111' . $tracking,
            'shortname' => 'cibcourse1' . $tracking,
        ];

        // Create course.
        if (!$course = $DB->get_record('course', $data)) {
            $course = generator::create_course($data);
        }

        $entity = new \local_intellidata\entities\courses\course($course);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'courses']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $course->id, 'idnumber' => $data['idnumber']]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
