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
 * Unit tests.
 *
 * @package filter_activitynames
 * @category test
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/activitynames/filter.php'); // Include the code to test.

/**
 * Test case for the activity names auto-linking filter.
 *
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_activitynames_filter_testcase extends advanced_testcase {

    public function test_links() {
        global $CFG;
        $this->resetAfterTest(true);

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Create two pages that will be linked to.
        $page1 = $this->getDataGenerator()->create_module('page',
                ['course' => $course->id, 'name' => 'Test 1']);
        $page2 = $this->getDataGenerator()->create_module('page',
                ['course' => $course->id, 'name' => 'Test (2)']);

        // Format text with all three entries in HTML.
        $html = '<p>Please read the two pages Test 1 and <i>Test (2)</i>.</p>';
        $filtered = format_text($html, FORMAT_HTML, array('context' => $context));

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~<a class="autolink" title="([^"]*)" href="[^"]*/mod/page/view.php\?id=([0-9]+)">([^<]*)</a>~',
                $filtered, $matches);

        // There should be 3 links links.
        $this->assertEquals(2, count($matches[1]));

        // Check text of title attribute.
        $this->assertEquals($page1->name, $matches[1][0]);
        $this->assertEquals($page2->name, $matches[1][1]);

        // Check the ids in the links.
        $this->assertEquals($page1->cmid, $matches[2][0]);
        $this->assertEquals($page2->cmid, $matches[2][1]);

        // Check the link text.
        $this->assertEquals($page1->name, $matches[3][0]);
        $this->assertEquals($page2->name, $matches[3][1]);
    }
}
