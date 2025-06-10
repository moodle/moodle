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
 * User logins migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\userlogins\userlogin;
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
require_once($CFG->dirroot . '/user/profile/definelib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * User logins migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class userlogins_test extends custom_db_client_testcase {

    /**
     * Test user loggedin.
     *
     * @covers \local_intellidata\entities\userlogins\userlogin
     * @covers \local_intellidata\entities\userlogins\migration
     * @covers \local_intellidata\entities\userlogins\observer::user_loggedin
     */
    public function test_loggedin() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->user_loggedin_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->user_loggedin_test(0);
    }

    /**
     * Test user loggedout.
     *
     * @covers \local_intellidata\entities\userlogins\userlogin
     * @covers \local_intellidata\entities\userlogins\migration
     * @covers \local_intellidata\entities\userlogins\observer::user_loggedout
     */
    public function test_loggedout() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->user_loggedout_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->user_loggedout_test(0);
    }

    /**
     * User loggedout test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function user_loggedout_test($tracking) {
        global $DB;

        $userdata = [
            'firstname' => 'ibactivitycompletionuser' . $tracking,
            'username' => 'ibactivitycompletionuser' . $tracking,
        ];

        $user = $DB->get_record('user', $userdata);
        if ($tracking == 0) {
            // Trigger loggout event.
            \core\event\user_loggedout::create(
                [
                    'userid' => $user->id,
                    'objectid' => $user->id,
                    'other' => ['username' => $user->username],
                ]
            )->trigger();
        } else {
            $this->insert_logstore('\core\event\user_loggedout', $user->id);
        }

        $data = [
            'id' => $user->id,
            'logins' => 1,
        ];

        $entity = new userlogin((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userlogins']);

        $datarecord = $storage->get_log_entity_data($tracking == 0 ? 'r' : 'c', ['id' => $user->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * User loggedin test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function user_loggedin_test($tracking) {
        $userdata = [
            'firstname' => 'ibactivitycompletionuser' . $tracking,
            'username' => 'ibactivitycompletionuser' . $tracking,
            'password' => 'Ibactivitycompletionuser!' . $tracking,
        ];

        // Create User.
        $user = generator::create_user($userdata);

        if ($tracking == 0) {
            // Trigger login event.
            \core\event\user_loggedin::create(
                [
                    'userid' => $user->id,
                    'objectid' => $user->id,
                    'other' => ['username' => $user->username],
                ]
            )->trigger();
        } else {
            $this->insert_logstore('\core\event\user_loggedin', $user->id);
        }

        $data = [
            'id' => $user->id,
            'logins' => 1,
        ];

        $entity = new userlogin((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userlogins']);
        $datarecord = $storage->get_log_entity_data($tracking == 0 ? 'r' : 'c', ['id' => $user->id]);

        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Insert logstore data.
     *
     * @param string $event
     * @param int $userid
     *
     * @return void
     * @throws \moodle_exception
     */
    private function insert_logstore($event, $userid) {
        global $DB;

        $record = (object) [
            'edulevel' => 0,
            'contextid' => 1,
            'contextlevel' => 50,
            'contextinstanceid' => 1,
            'eventname' => $event,
            'userid' => $userid,
            'crud' => 'r',
            'timecreated' => time(),
        ];

        $DB->insert_record('logstore_standard_log', $record);
    }
}
