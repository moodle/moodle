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
 * User migration test case.
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
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * User migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class users_test extends custom_db_client_testcase {

    /**
     * Test user create.
     *
     * @covers \local_intellidata\entities\users\user
     * @covers \local_intellidata\entities\users\migration
     * @covers \local_intellidata\entities\users\observer::user_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_user_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_user_test(0);
    }

    /**
     * Test user update.
     *
     * @covers \local_intellidata\entities\users\user
     * @covers \local_intellidata\entities\users\migration
     * @covers \local_intellidata\entities\users\observer::user_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_user_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_user_test(0);
    }

    /**
     * Test user delete.
     *
     * @covers \local_intellidata\entities\users\user
     * @covers \local_intellidata\entities\users\migration
     * @covers \local_intellidata\entities\users\observer::user_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_user_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_user_test(0);
    }

    /**
     * Delete user test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_user_test($tracking) {
        global $DB;

        $data = [
            'username' => 'aunittest_create_user' . $tracking,
        ];

        $user = $DB->get_record('user', $data);

        user_delete_user($user);

        $entity = new \local_intellidata\entities\users\user($user);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'users']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $user->id]);

        $datarecorddata = json_decode($datarecord->data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update user test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_user_test($tracking) {
        global $DB;

        $data = [
            'username' => 'aunittest_create_user' . $tracking,
        ];

        $user = $DB->get_record('user', $data);
        $user->firstname = 'unit test update user';
        $data['firstname'] = $user->firstname;

        user_update_user($user, false);

        $entity = new \local_intellidata\entities\users\user($user);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'users']);
        $datarecord = $storage->get_log_entity_data('u', ['id' => $user->id, 'firstname' => $user->firstname]);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create user test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_user_test($tracking) {
        $data = [
            'firstname' => 'unit test create user',
            'username' => 'aunittest_create_user' . $tracking,
            'password' => 'Unittest_User1!',
        ];

        // Create user.
        $user = generator::create_user($data);

        $entity = new \local_intellidata\entities\users\user($user);
        $entitydata = $entity->export();

        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'users']);
        $datarecord = $storage->get_log_entity_data('c', ['id' => $user->id]);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
