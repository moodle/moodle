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
 * Tests for report library functions.
 *
 * @package    report_usersessions
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/tests/fixtures/myprofile_fixtures.php');
require_once($CFG->dirroot. '/report/usersessions/lib.php');

/**
 * Class report_stats_lib_testcase
 *
 * @package    report_usersessions
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class report_usersessions_lib_testcase extends advanced_testcase {

    /**
     * Tests for report_userssesions_myprofile_navigation() api.
     */
    public function test_report_usersessions_myprofile_navigation() {

        $this->resetAfterTest();
        $this->setAdminUser();

        $tree = new phpunit_fixture_myprofile_tree();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $iscurrentuser = false;

        // Not even admins allowed to pick at other user's sessions.
        report_usersessions_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('usersessions', $nodes);

        // Try as guest user.
        $this->setGuestUser();
        report_usersessions_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('usersessions', $nodes);

        // As current user.
        $this->setUser($user);
        $tree = new phpunit_fixture_myprofile_tree();
        $iscurrentuser = true;
        report_usersessions_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('usersessions', $nodes);

        // Try to see as a user without permission.
        $this->setUser($user2);
        $tree = new phpunit_fixture_myprofile_tree();
        $iscurrentuser = true;
        report_usersessions_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('usersessions', $nodes);

    }
}
