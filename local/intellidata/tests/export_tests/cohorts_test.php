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
 * Cohort migration test case.
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
 * Cohort migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class cohorts_test extends custom_db_client_testcase {

    /**
     * Test cohort create.
     *
     * @covers \local_intellidata\entities\cohorts\cohort
     * @covers \local_intellidata\entities\cohorts\migration
     * @covers \local_intellidata\entities\cohorts\observer::cohort_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_cohort_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_cohort_test(0);
    }

    /**
     * Test cohort update.
     *
     * @covers \local_intellidata\entities\cohorts\cohort
     * @covers \local_intellidata\entities\cohorts\migration
     * @covers \local_intellidata\entities\cohorts\observer::cohort_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_cohort_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_cohort_test(0);
    }

    /**
     * Test cohort delete.
     *
     * @covers \local_intellidata\entities\cohorts\cohort
     * @covers \local_intellidata\entities\cohorts\migration
     * @covers \local_intellidata\entities\cohorts\observer::cohort_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_cohort_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_cohort_test(0);
    }

    /**
     * Delete cohort test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function delete_cohort_test($tracking) {
        global $DB;

        $data = [
            'name' => 'ibcohort1' . $tracking,
        ];

        $cohort = $DB->get_record('cohort', $data);

        cohort_delete_cohort($cohort);

        $entity = new \local_intellidata\entities\cohorts\cohort($cohort);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'cohorts']);

        $datarecord = $storage->get_log_entity_data('d', ['id' => $cohort->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = json_decode($datarecord->data);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update cohort test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function update_cohort_test($tracking) {
        global $DB;
        $data = [
            'name' => 'ibcohort1' . $tracking,
        ];

        $cohort = $DB->get_record('cohort', $data);
        $cohort->contextid = '2';
        $data['contextid'] = $cohort->contextid;

        cohort_update_cohort($cohort);

        $entity = new \local_intellidata\entities\cohorts\cohort($cohort);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'cohorts']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $cohort->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create cohort test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function create_cohort_test($tracking) {
        global $DB;
        $data = [
            'name' => 'ibcohort1' . $tracking,
            'contextid' => '1',
        ];

        // Create cohort.
        if (!$cohort = $DB->get_record('cohort', $data)) {
            $cohort = generator::create_cohort($data);
        }

        $entity = new \local_intellidata\entities\cohorts\cohort($cohort);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'cohorts']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $cohort->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
