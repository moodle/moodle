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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/notes/lib.php');
require_once($CFG->dirroot . '/user/tests/fixtures/myprofile_fixtures.php');
/**
 * Class core_notes_lib_testcase
 *
 * @package    core_notes
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class core_notes_lib_testcase extends advanced_testcase {

    /**
     * Tests for core_notes_myprofile_navigation() api.
     */
    public function test_core_notes_myprofile_navigation() {
        global $USER;

        $this->resetAfterTest();
        $this->setGuestUser();

        // No notes for guest users.
        $tree = new phpunit_fixture_myprofile_tree();
        $course = null;
        $iscurrentuser = false;
        core_notes_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('blogs', $nodes);

        // Disable notes.
        $this->setAdminUser();
        set_config('enablenotes', false);
        $tree = new phpunit_fixture_myprofile_tree();
        core_notes_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('notes', $nodes);

        // Enable notes.
        set_config('enablenotes', true);
        $tree = new phpunit_fixture_myprofile_tree();
        $iscurrentuser = true;
        core_notes_myprofile_navigation($tree, $USER, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('notes', $nodes);
    }
}