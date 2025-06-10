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
 * Participation migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\participations\participation;
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
 * Participation migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class participation_test extends custom_db_client_testcase {

    /**
     * Test participation create.
     *
     * @covers \local_intellidata\entities\participations\participation
     * @covers \local_intellidata\entities\participations\migration
     * @covers \local_intellidata\entities\participations\observer::new_participation
     */
    public function test_new() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        set_config('loglifetime', 100);

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->new_participation_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->new_participation_test(0);
    }

    /**
     * Participation create test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function new_participation_test($tracking) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/course/lib.php');

        if ($tracking == 0) {
            $coursedata = [
                'fullname' => 'testnp' . $tracking,
                'idnumber' => '111111np' . $tracking,
                'shortname' => 'testnp' . $tracking,
            ];

            $course = generator::create_course($coursedata);

            generator::create_module('page', ['course' => $course->id]);
        } else {
            $record = (object) [
                'edulevel' => 0,
                'contextid' => 1,
                'contextlevel' => CONTEXT_MODULE,
                'contextinstanceid' => 1,
                'userid' => 2,
                'crud' => 'c',
                'timecreated' => time(),
            ];

            $DB->insert_record('logstore_standard_log', $record);
        }

        $data = [
            'userid' => 2,
            'participations' => 1,
            'type' => 'activity',
        ];

        $entity = new participation((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'participation']);
        $datarecord = $storage->get_log_entity_data('c', ['userid' => 2, 'type' => 'activity']);

        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertEquals($entitydata, $datarecorddata);
    }
}
