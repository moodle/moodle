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
 * Tests for gradereport_user library functions.
 *
 * @package    gradereport_user
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/tests/fixtures/myprofile_fixtures.php');
require_once($CFG->dirroot . '/grade/report/user/lib.php');

/**
 * Class gradereport_user_lib_testcase.
 *
 * @package    gradereport_user
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class gradereport_user_lib_testcase extends advanced_testcase {

    /**
     * Tests for gradereport_user_myprofile_navigation() api.
     */
    public function test_gradereport_user_myprofile_navigation() {

        $this->resetAfterTest();
        $this->setAdminUser();

        // User with all permissions.
        $tree = new phpunit_fixture_myprofile_tree();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $iscurrentuser = false;

        gradereport_user_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('grade', $nodes);

        // Try to see as a user without permission.
        $this->setUser($user2);
        $tree = new phpunit_fixture_myprofile_tree();
        $iscurrentuser = true;
         gradereport_user_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('grade', $nodes);
    }
}
