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
 * Course sections migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\coursesections\sections;
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
 * Course sections migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class coursesections_test extends custom_db_client_testcase {

    /**
     * Test course sections create.
     *
     * @covers \local_intellidata\entities\coursesections\sections
     * @covers \local_intellidata\entities\coursesections\migration
     * @covers \local_intellidata\entities\coursesections\observer::course_section_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_coursesections_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_coursesections_test(0);
    }

    /**
     * Test course sections update.
     *
     * @covers \local_intellidata\entities\coursesections\sections
     * @covers \local_intellidata\entities\coursesections\migration
     * @covers \local_intellidata\entities\coursesections\observer::course_section_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_coursesections_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_coursesections_test(0);
    }

    /**
     * Test course sections delete.
     *
     * @covers \local_intellidata\entities\coursesections\sections
     * @covers \local_intellidata\entities\coursesections\migration
     * @covers \local_intellidata\entities\coursesections\observer::course_section_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_coursesections_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_coursesections_test(0);
    }

    /**
     * Test course module create.
     *
     * @covers \local_intellidata\entities\coursesections\sections
     * @covers \local_intellidata\entities\coursesections\migration
     * @covers \local_intellidata\entities\coursesections\observer::course_module_created
     */
    public function test_module_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_module_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_module_test(0);
    }

    /**
     * Create course module test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_module_test($tracking) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/course/lib.php');

        $coursedata = [
            'fullname' => 'testsections' . $tracking,
            'idnumber' => '11111112' . $tracking,
            'enablecompletion' => true,
        ];

        $course = $DB->get_record('course', $coursedata);

        $page = generator::create_module('page', ['course' => $course->id]);

        $cm = get_coursemodule_from_instance('page', $page->id, $page->course);

        $data = [
            'id' => $cm->section,
            'course' => $page->course,
            'sequence' => $cm->id,
        ];

        $entity = new sections($data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'coursesections']);

        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Delete course section test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_coursesections_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'testsections' . $tracking,
            'idnumber' => '11111112' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);
        $sectionparams = [
            'course' => $course->id,
            'section' => 6,
        ];
        $section = $DB->get_record('course_sections', $sectionparams);

        $data = [
            'id' => $section->id,
        ];

        course_delete_section($course, $section, true, true);

        $entity = new sections((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'coursesections']);
        $datarecord = $storage->get_log_entity_data('d', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update course section test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_coursesections_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'testsections' . $tracking,
            'idnumber' => '11111112' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);
        $sectionparams = [
            'course' => $course->id,
            'section' => 6,
        ];
        $section = $DB->get_record('course_sections', $sectionparams);

        course_update_section($course, $section, ['name' => 'test' . $tracking]);

        $sectionparams['name'] = 'test' . $tracking;

        $entity = new sections($sectionparams);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $sectionparams);

        $storage = StorageHelper::get_storage_service(['name' => 'coursesections']);
        $datarecord = $storage->get_log_entity_data('u', $sectionparams);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $sectionparams);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create course section test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_coursesections_test($tracking) {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $coursedata = [
            'fullname' => 'testsections' . $tracking,
            'idnumber' => '11111112' . $tracking,
            'shortname' => 'testsections' . $tracking,
            'enablecompletion' => true,
        ];

        $course = generator::create_course($coursedata);

        // Add a section to the course.
        $section = course_create_section($course->id);

        $data = [
            'id' => $section->id,
            'section' => $section->section,
            'course' => $course->id,
            'name' => get_section_name($section->course, $section->section),
        ];

        $entity = new sections($section);
        $entitydata = $entity->export();

        $entitydata->name = $data['name'];

        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'coursesections']);

        $datarecord = $storage->get_log_entity_data('c', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
