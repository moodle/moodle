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
namespace gradereport_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/report/user/lib.php');

/**
 * Class gradereport_user_lib_testcase.
 *
 * @package    gradereport_user
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
final class lib_test extends \advanced_testcase {

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
        parent::setUp();
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->tree = new \core_user\output\myprofile\tree();
        $this->resetAfterTest();
    }

    /**
     * Tests the gradereport_user_myprofile_navigation() function.
     */
    public function test_gradereport_user_myprofile_navigation(): void {
        $this->setAdminUser();
        $iscurrentuser = false;

        gradereport_user_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('grade', $nodes->getValue($this->tree));
    }

    /**
     * Tests the gradereport_user_myprofile_navigation() function for a user
     * without permission to view the grade node.
     */
    public function test_gradereport_user_myprofile_navigation_without_permission(): void {
        $this->setUser($this->user);
        $iscurrentuser = true;

        gradereport_user_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('grade', $nodes->getValue($this->tree));
    }
}
