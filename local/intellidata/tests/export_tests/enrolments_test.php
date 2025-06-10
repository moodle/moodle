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
 * Enrol migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

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
 * Enrol migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class enrolments_test extends custom_db_client_testcase {

    /**
     * Test enrol create.
     *
     * @covers \local_intellidata\entities\enrolments\enrolment
     * @covers \local_intellidata\entities\enrolments\migration
     * @covers \local_intellidata\entities\enrolments\observer::user_enrolment_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_enrol_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_enrol_test(0);
    }

    /**
     * Test enrol update.
     *
     * @covers \local_intellidata\entities\enrolments\enrolment
     * @covers \local_intellidata\entities\enrolments\migration
     * @covers \local_intellidata\entities\enrolments\observer::user_enrolment_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_enrol_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_enrol_test(0);
    }

    /**
     * Test enrol delete.
     *
     * @covers \local_intellidata\entities\enrolments\enrolment
     * @covers \local_intellidata\entities\enrolments\migration
     * @covers \local_intellidata\entities\enrolments\observer::user_enrolment_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_enrol_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_enrol_test(0);
    }

    /**
     * Enrol delete test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_enrol_test($tracking) {
        global $DB;

        $userdata = [
            'firstname' => 'unit test enrol user',
            'username' => 'unittest_enrol_user' . $tracking,
        ];

        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'unit test enrol course' . $tracking,
            'idnumber' => '111222' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);

        $userenroldata = [
            'userid' => $user->id,
        ];

        $userenrol = $DB->get_record('user_enrolments', $userenroldata);

        $enroldata = [
            'id' => $userenrol->enrolid,
            'courseid' => $course->id,
        ];

        $enrol = $DB->get_record('enrol', $enroldata);

        $plugin = enrol_get_plugin('manual');

        $plugin->unenrol_user($enrol, $user->id);

        $entity = new \local_intellidata\entities\enrolments\enrolment($userenrol);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'enrolments']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $userenrol->id]);
        $datarecorddata = json_decode($datarecord->data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Enrol update test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_enrol_test($tracking) {
        global $DB;

        $userdata = [
            'firstname' => 'unit test enrol user',
            'username' => 'unittest_enrol_user' . $tracking,
        ];

        $user = $DB->get_record('user', $userdata);

        $coursedata = [
            'fullname' => 'unit test enrol course' . $tracking,
            'idnumber' => '111222' . $tracking,
        ];

        $course = $DB->get_record('course', $coursedata);

        $userenroldata = [
            'userid' => $user->id,
        ];

        $userenrol = $DB->get_record('user_enrolments', $userenroldata);

        $enroldata = [
            'id' => $userenrol->enrolid,
            'courseid' => $course->id,
        ];

        $enrol = $DB->get_record('enrol', $enroldata);

        $data = [
            'userid' => $user->id,
            'courseid' => $course->id,
        ];

        $plugin = enrol_get_plugin('manual');

        $plugin->update_user_enrol($enrol, $user->id, ENROL_USER_ACTIVE, time(), time());

        $entity = new \local_intellidata\entities\enrolments\enrolment((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'enrolments']);
        $datarecord = $storage->get_log_entity_data('u', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Enrol create test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_enrol_test($tracking) {

        $userdata = [
            'firstname' => 'unit test enrol user',
            'username' => 'unittest_enrol_user' . $tracking,
            'password' => 'Unittest_User1!',
        ];

        // Create user.
        $user = generator::create_user($userdata);

        $coursedata = [
            'fullname' => 'unit test enrol course' . $tracking,
            'idnumber' => '111222' . $tracking,
            'shortname' => 'testibenrolments' . $tracking,
        ];

        // Create course.
        $course = generator::create_course($coursedata);

        $data = [
            'userid' => $user->id,
            'courseid' => $course->id,
        ];

        // Enrol user.
        generator::enrol_user($data);

        $entity = new \local_intellidata\entities\enrolments\enrolment((object)$data);
        $entitydata = $entity->export();

        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'enrolments']);
        $datarecord = $storage->get_log_entity_data('c', $data);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
