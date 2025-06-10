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
 * Activity Completion migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use completion_info;
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
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Activity Completion migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class activitycompletions_test extends custom_db_client_testcase {

    /**
     * Test update activity completion.
     *
     * @covers \local_intellidata\entities\activitycompletions\activitycompletion
     * @covers \local_intellidata\entities\activitycompletions\migration
     * @covers \local_intellidata\entities\activitycompletions\observer::course_module_completion_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_activitycomplations_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_activitycomplations_test(0);
    }

    /**
     * Update activity completion test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function update_activitycomplations_test($tracking) {
        $userdata = [
            'firstname' => 'ibactivitycompletionuser1' . $tracking,
            'username' => 'ibactivitycompletionuser1' . $tracking,
            'password' => 'Ibactivitycompletionuser1!',
        ];

        // Create User.
        $user = generator::create_user($userdata);

        $coursedata = [
            'fullname' => 'ibcourseactivity1' . $tracking,
            'shortname' => 'ibcourseactivity1' . $tracking,
        ];

        // Create Course.
        $course = generator::create_course($coursedata);

        // Create Module.
        $page = generator::create_module('page', ['course' => $course->id]);

        $data = [
            "coursemoduleid" => $page->cmid,
            "userid" => $user->id,
        ];

        $idata = (object)$data;
        $idata->id = 0;
        $idata->completionstate = COMPLETION_COMPLETE;
        $idata->timemodified = time();
        $idata->viewed = COMPLETION_NOT_VIEWED;
        $idata->timecompleted = null;
        $idata->reaggregate = 0;
        $idata->overrideby = 0;

        $c = new completion_info($course);
        $c->internal_set_data($page, $idata);

        $entity = new \local_intellidata\entities\activitycompletions\activitycompletion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'activitycompletions']);
        $datarecord = $storage->get_log_entity_data($tracking == 1 ? 'c' : 'u', $data);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
