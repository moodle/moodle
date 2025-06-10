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
 * User Grade migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use grade_category;
use grade_grade;
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
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * User Grade migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class usergrades_test extends custom_db_client_testcase {

    /**
     * Test graded.
     *
     * @covers \local_intellidata\entities\usergrades\usergrade
     * @covers \local_intellidata\entities\usergrades\migration
     * @covers \local_intellidata\entities\usergrades\observer
     */
    public function test_graded() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->graded_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->graded_test(0);
    }

    /**
     * Graded test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function graded_test($tracking) {
        $userdata = [
            'firstname' => 'unit test grade user',
            'username' => 'unittest_grade_user' . $tracking,
            'password' => 'Unittest_GradeUser1!',
        ];

        // Create User.
        $user = generator::create_user($userdata);

        // Create Course.
        $course = generator::create_course(['shortname' => 'ibusergradecourse' . $tracking]);

        $data = [
            'userid' => $user->id,
            'courseid' => $course->id,
        ];

        // Enrol User.
        generator::enrol_user($data);

        $gradecategory = grade_category::fetch_course_category($course->id);
        $gradecategory->load_grade_item();

        $gradeitem = $gradecategory->grade_item;
        $gradeitem->update_final_grade($user->id, 10, 'gradebook');

        $gradegrade = new grade_grade(['userid' => $user->id, 'itemid' => $gradeitem->id], true);
        $gradegrade->grade_item = $gradeitem;

        \core\event\user_graded::create_from_grade($gradegrade);

        unset($data['courseid']);

        $entity = new \local_intellidata\entities\usergrades\usergrade($gradegrade);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'usergrades']);
        $datarecord = $storage->get_log_entity_data($tracking == 1 ? 'c' : 'u', $data);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
