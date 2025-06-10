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
 * PHPUnit data generator tests.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');

use local_intellidata\persistent\tracking;


/**
 * PHPUnit data generator testcase
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class generator_test extends \advanced_testcase {

    /**
     * Setup tests.
     *
     * @return void
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
    }

    /**
     * Test tracking generator.
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     * @covers \local_intellidata\generator::create_tracking
     */
    public function test_tracking_generator() {

        // Validate empty table.
        $this->assertEquals(0, tracking::count_records());

        // Validate generator instance.
        $generator = generator::data_plugin_generator();
        $this->assertInstanceOf('local_intellidata_generator', $generator);

        // Create user.
        $userdata = [
            'firstname' => 'unit test create user',
            'username' => 'unittest_create_user',
            'password' => 'Unittest_User1!',
        ];
        $user = generator::create_user($userdata);

        // Create tracking.
        $tracking = generator::create_tracking(['userid' => $user->id]);

        $this->assertEquals(1, tracking::count_records());

        $savedtracking = tracking::get_record(['id' => $tracking->id]);
        $this->assertEquals($savedtracking->get('userid'), $user->id);
    }
}
