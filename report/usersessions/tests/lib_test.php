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
     * @var stdClass The user.
     */
    private $user;

    /**
     * @var stdClass The course.
     */
    private $course;

    /**
     * @var \core_user\output\myprofile\tree The navigation tree.
     */
    private $tree;

    public function setUp(): void {
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->tree = new \core_user\output\myprofile\tree();
        $this->resetAfterTest();
    }

    /**
     * Tests the report_userssesions_myprofile_navigation() function as an admin.
     */
    public function test_report_usersessions_myprofile_navigation_as_admin() {
        $this->setAdminUser();
        $iscurrentuser = false;

        // Not even admins allowed to pick at other user's sessions.
        report_usersessions_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayNotHasKey('usersessions', $nodes->getValue($this->tree));
    }

    /**
     * Tests the report_userssesions_myprofile_navigation() function as the currently logged in user.
     */
    public function test_report_usersessions_myprofile_navigation_as_current_user() {
        $this->setUser($this->user);
        $iscurrentuser = true;

        report_usersessions_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayHasKey('usersessions', $nodes->getValue($this->tree));
    }

    /**
     * Tests the report_userssesions_myprofile_navigation() function as a guest.
     */
    public function test_report_usersessions_myprofile_navigation_as_guest() {
        $this->setGuestUser();
        $iscurrentuser = true;

        report_usersessions_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayNotHasKey('usersessions', $nodes->getValue($this->tree));
    }

    /**
     * Tests the report_userssesions_myprofile_navigation() function as a user without permission.
     */
    public function test_report_usersessions_myprofile_navigation_without_permission() {
        // Try to see as a user without permission.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);
        $iscurrentuser = false;

        report_usersessions_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayNotHasKey('usersessions', $nodes->getValue($this->tree));

    }
}
