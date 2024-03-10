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
 * Tests for notes library functions.
 *
 * @package    core_notes
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace core_notes;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/notes/lib.php');
/**
 * Class core_notes_lib_testcase
 *
 * @package    core_notes
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class lib_test extends \advanced_testcase {

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
     * Tests the core_notes_myprofile_navigation() function.
     */
    public function test_core_notes_myprofile_navigation() {
        global $USER;

        // Set up the test.
        $this->setAdminUser();
        $iscurrentuser = true;

        // Enable notes.
        set_config('enablenotes', true);

        // Check the node tree is correct.
        core_notes_myprofile_navigation($this->tree, $USER, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('notes', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_notes_myprofile_navigation() function.
     */
    public function test_core_notes_myprofile_navigation_as_guest() {
        global $USER;

        $this->setGuestUser();
        $iscurrentuser = false;

        // Check the node tree is correct.
        core_notes_myprofile_navigation($this->tree, $USER, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('notes', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_notes_myprofile_navigation() function.
     */
    public function test_core_notes_myprofile_navigation_notes_disabled() {
        global $USER;

        $this->setAdminUser();
        $iscurrentuser = false;

        // Disable notes.
        set_config('enablenotes', false);

        // Check the node tree is correct.
        core_notes_myprofile_navigation($this->tree, $USER, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('notes', $nodes->getValue($this->tree));
    }
}
