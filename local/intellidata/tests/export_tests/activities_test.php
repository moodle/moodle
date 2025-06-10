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
 * Activity migration test case.
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
 * Activity migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class activities_test extends custom_db_client_testcase {

    /**
     * Test activity create.
     *
     * @covers \local_intellidata\entities\activities\activity
     * @covers \local_intellidata\entities\activities\migration
     * @covers \local_intellidata\entities\activities\observer::course_module_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_activity_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_activity_test(0);
    }

    /**
     * Test activity update.
     *
     * @covers \local_intellidata\entities\activities\activity
     * @covers \local_intellidata\entities\activities\migration
     * @covers \local_intellidata\entities\activities\observer::course_module_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_activity_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_activity_test(0);
    }

    /**
     * Test activity delete.
     *
     * @covers \local_intellidata\entities\activities\activity
     * @covers \local_intellidata\entities\activities\migration
     * @covers \local_intellidata\entities\activities\observer::course_module_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_activity_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_activity_test(0);
    }

    /**
     * Update activity test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_activity_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'ibcourseactivity1' . $tracking,
            'idnumber' => '1111111' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);

        $page = generator::create_module('page', ['course' => $course->id]);

        $data = [
            'courseid' => $course->id,
            'id' => $page->cmid,
        ];

        set_coursemodule_name($data['id'], 'modulename');

        $entity = new \local_intellidata\entities\activities\activity((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'activities']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create activity test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_activity_test($tracking) {
        $coursedata = [
            'fullname' => 'ibcourseactivity1' . $tracking,
            'idnumber' => '1111111' . $tracking,
        ];

        $course = generator::create_course($coursedata);

        $page = generator::create_module('page', ['course' => $course->id]);

        $data = [
            'courseid' => $course->id,
            'id' => $page->cmid,
        ];

        $entity = new \local_intellidata\entities\activities\activity((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'activities']);
        $datarecord = $storage->get_log_entity_data('c', $data);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Delete activity test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_activity_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'ibcourseactivity1' . $tracking,
            'idnumber' => '1111111' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);

        $page = generator::create_module('page', ['course' => $course->id]);

        $data = [
            'id' => $page->cmid,
        ];

        course_delete_module($page->cmid);

        $entity = new \local_intellidata\entities\activities\activity((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'activities']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $data['id']]);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }
}
