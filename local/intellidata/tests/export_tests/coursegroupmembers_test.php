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
 * Course group members migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
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
 * Course group members migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class coursegroupmembers_test extends custom_db_client_testcase {

    /**
     * Test course group create.
     *
     * @covers \local_intellidata\entities\groups\group
     * @covers \local_intellidata\entities\groups\migration
     * @covers \local_intellidata\entities\groups\observer::group_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_group_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_group_test(0);
    }

    /**
     * Test course group member create.
     *
     * @covers \local_intellidata\entities\groupmembers\groupmember
     * @covers \local_intellidata\entities\groupmembers\migration
     * @covers \local_intellidata\entities\groupmembers\observer::group_member_added
     */
    public function test_create_member() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_member_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_member_test(0);
    }

    /**
     * Test course group update.
     *
     * @covers \local_intellidata\entities\groups\group
     * @covers \local_intellidata\entities\groups\migration
     * @covers \local_intellidata\entities\groups\observer::group_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_group_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_group_test(0);
    }

    /**
     * Test course group member delete.
     *
     * @covers \local_intellidata\entities\groupmembers\groupmember
     * @covers \local_intellidata\entities\groupmembers\migration
     * @covers \local_intellidata\entities\groupmembers\observer::group_member_removed
     */
    public function test_delete_member() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_groupmember_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_groupmember_test(0);
    }

    /**
     * Test course group delete.
     *
     * @covers \local_intellidata\entities\groups\group
     * @covers \local_intellidata\entities\groups\migration
     * @covers \local_intellidata\entities\groups\observer::group_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_group_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_group_test(0);
    }

    /**
     * Create course group member test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_member_test($tracking) {
        global $DB;

        $gdata = [
            'name' => 'testgroup1g' . $tracking,
        ];
        $group = $DB->get_record('groups', $gdata);

        $userdata = [
            'firstname' => 'ibuser1g',
            'username' => 'ibuser1g' . $tracking,
            'password' => 'Ibuser1!',
        ];

        // Create user.
        $user = generator::create_user($userdata);

        $data = [
            'userid' => $user->id,
            'courseid' => $group->courseid,
        ];

        // Enrol user.
        generator::enrol_user($data);

        $gmdata = [
            'groupid' => $group->id,
            'userid' => $user->id,
        ];

        // Assign user to group.
        generator::create_group_member($gmdata);

        $groupm = $DB->get_record('groups_members', $gmdata);

        $entity = new \local_intellidata\entities\groupmembers\groupmember($groupm);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $gmdata);

        $storage = StorageHelper::get_storage_service(['name' => 'coursegroupmembers']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $groupm->id]);
        $this->assertNotEmpty($datarecord);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $gmdata);

        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Delete course group test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_group_test($tracking) {
        global $DB;

        $gdata = [
            'name' => 'testgroupupdateg' . $tracking,
        ];
        $group = $DB->get_record('groups', $gdata);

        groups_delete_group($group);

        $entity = new \local_intellidata\entities\groups\group($group);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'coursegroups']);

        $datarecord = $storage->get_log_entity_data('d', ['id' => $group->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = json_decode($datarecord->data);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update course group test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_group_test($tracking) {
        global $DB;

        $gdata = [
            'name' => 'testgroup1g' . $tracking,
        ];
        $group = $DB->get_record('groups', $gdata);
        $group->name = 'testgroupupdateg' . $tracking;
        $gdata['name'] = $group->name;

        groups_update_group($group);

        $entity = new \local_intellidata\entities\groups\group($group);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $gdata);

        $storage = StorageHelper::get_storage_service(['name' => 'coursegroups']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $group->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $gdata);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Delete course group member test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_groupmember_test($tracking) {
        global $DB;

        $gdata = [
            'name' => 'testgroupupdateg' . $tracking,
        ];
        $group = $DB->get_record('groups', $gdata);

        $userdata = [
            'firstname' => 'ibuser1g',
            'username' => 'ibuser1g' . $tracking,
        ];
        $user = $DB->get_record('user', $userdata);

        groups_remove_member($group->id, $user->id);

        $groupm = [
            'userid' => $user->id,
            'groupid' => $group->id,
        ];
        $entity = new \local_intellidata\entities\groupmembers\groupmember($groupm);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $groupm);

        $storage = StorageHelper::get_storage_service(['name' => 'coursegroupmembers']);

        $datarecord = $storage->get_log_entity_data('d', $groupm);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $groupm);

        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create course group test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_group_test($tracking) {
        $data = [
            'fullname' => 'ibcourse1g' . $tracking,
            'idnumber' => '1111111g' . $tracking,
            'shortname' => 'ibcourse1g' . $tracking,
        ];

        // Create course.
        $course = generator::create_course($data);

        $gdata = [
            'name' => 'testgroup1g' . $tracking,
            'courseid' => $course->id,
        ];

        // Create group.
        $group = generator::create_group($gdata);

        $entity = new \local_intellidata\entities\groups\group($group);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $gdata);

        $storage = StorageHelper::get_storage_service(['name' => 'coursegroups']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $group->id]);
        $this->assertNotEmpty($datarecord);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $gdata);

        $this->assertEquals($entitydata, $datarecorddata);
    }
}
