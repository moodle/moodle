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
 * Cohort members migration test case.
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
 * Cohort members migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class cohortmembers_test extends custom_db_client_testcase {

    /**
     * Test cohort member create.
     *
     * @covers \local_intellidata\entities\cohortmembers\cohortmember
     * @covers \local_intellidata\entities\cohortmembers\migration
     * @covers \local_intellidata\entities\cohortmembers\observer::cohort_member_added
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_cohortmember_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_cohortmember_test(0);
    }

    /**
     * Test cohort member delete.
     *
     * @covers \local_intellidata\entities\cohortmembers\cohortmember
     * @covers \local_intellidata\entities\cohortmembers\migration
     * @covers \local_intellidata\entities\cohortmembers\observer::cohort_member_removed
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_cohortmember_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_cohortmember_test(0);
    }

    /**
     * Delete cohort member test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_cohortmember_test($tracking) {
        global $DB;

        $userdata = [
            'firstname' => 'ibuser1',
            'username' => 'ibuser1' . $tracking,
        ];

        $user = $DB->get_record('user', $userdata);

        $cohortdata = [
            'name' => 'ibcohort1' . $tracking,
            'contextid' => '1',
        ];

        $cohort = $DB->get_record('cohort', $cohortdata);

        $data = [
            'cohortid' => $cohort->id,
            'userid' => $user->id,
        ];

        cohort_remove_member($cohort->id, $user->id);

        $entity = new \local_intellidata\entities\cohortmembers\cohortmember((object)$data);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'cohortmembers']);

        $datarecord = $storage->get_log_entity_data('d', $data);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = json_decode($datarecord->data);
        $this->assertEquals($entitydata->cohortid, $datarecorddata->cohortid);
        $this->assertEquals($entitydata->userid, $datarecorddata->userid);
    }

    /**
     * Create cohort member test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_cohortmember_test($tracking) {
        $userdata = [
            'firstname' => 'ibuser1',
            'username' => 'ibuser1' . $tracking,
            'password' => 'Ibuser1!',
        ];

        $user = generator::create_user($userdata);

        $cohortdata = [
            'name' => 'ibcohort1' . $tracking,
            'contextid' => '1',
        ];

        $cohort = generator::create_cohort($cohortdata);

        $data = [
            'cohortid' => $cohort->id,
            'userid' => $user->id,
        ];

        // Create cohortmember.
        cohort_add_member($data['cohortid'], $data['userid']);

        $entity = new \local_intellidata\entities\cohortmembers\cohortmember((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'cohortmembers']);

        $datarecord = $storage->get_log_entity_data('c', $data);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
