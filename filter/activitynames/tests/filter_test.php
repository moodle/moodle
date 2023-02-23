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

namespace filter_activitynames;

/**
 * Test case for the activity names auto-linking filter.
 *
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_test extends \advanced_testcase {

    public function test_links() {
        $this->resetAfterTest(true);

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

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

        // There should be 2 links links.
        $this->assertCount(2, $matches[1]);

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

    public function test_links_activity_named_hyphen() {
        $this->resetAfterTest(true);

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Work around an issue with the activity names filter which maintains a static cache
        // of activities for current course ID. We can re-build the cache by switching user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Create a page activity named '-' (single hyphen).
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => '-']);

        $html = '<p>Please read the - page.</p>';
        $filtered = format_text($html, FORMAT_HTML, array('context' => $context));

        // Find the page link in the filtered html.
        preg_match_all('~<a class="autolink" title="([^"]*)" href="[^"]*/mod/page/view.php\?id=([0-9]+)">([^<]*)</a>~',
            $filtered, $matches);

        // We should have exactly one match.
        $this->assertCount(1, $matches[1]);

        $this->assertEquals($page->name, $matches[1][0]);
        $this->assertEquals($page->cmid, $matches[2][0]);
        $this->assertEquals($page->name, $matches[3][0]);
    }

    public function test_cache() {
        $this->resetAfterTest(true);

        // Create a test courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        // Create page 1.
        $page1 = $this->getDataGenerator()->create_module('page',
            ['course' => $course1->id, 'name' => 'Test 1']);
        // Format text with page 1 in HTML.
        $html = '<p>Please read the two pages Test 1 and Test 2.</p>';
        $filtered1 = format_text($html, FORMAT_HTML, array('context' => $context1));
        // Find all the activity links in the result.
        $matches = [];
        preg_match_all('~<a class="autolink" title="([^"]*)" href="[^"]*/mod/page/view.php\?id=([0-9]+)">([^<]*)</a>~',
            $filtered1, $matches);
        // There should be 1 link.
        $this->assertCount(1, $matches[1]);
        $this->assertEquals($page1->name, $matches[1][0]);

        // Create page 2.
        $page2 = $this->getDataGenerator()->create_module('page',
        ['course' => $course1->id, 'name' => 'Test 2']);
        // Filter the text again.
        $filtered2 = format_text($html, FORMAT_HTML, array('context' => $context1));
        // The filter result does not change due to caching.
        $this->assertEquals($filtered1, $filtered2);

        // Change context, so that cache for course 1 is cleared.
        $filtered3 = format_text($html, FORMAT_HTML, array('context' => $context2));
        $this->assertNotEquals($filtered1, $filtered3);
        $matches = [];
        preg_match_all('~<a class="autolink" title="([^"]*)" href="[^"]*/mod/page/view.php\?id=([0-9]+)">([^<]*)</a>~',
            $filtered3, $matches);
        // There should be no links.
        $this->assertCount(0, $matches[1]);

        // Filter the text for course 1.
        $filtered4 = format_text($html, FORMAT_HTML, array('context' => $context1));
        // Find all the activity links in the result.
        $matches = [];
        preg_match_all('~<a class="autolink" title="([^"]*)" href="[^"]*/mod/page/view.php\?id=([0-9]+)">([^<]*)</a>~',
            $filtered4, $matches);
        // There should be 2 links.
        $this->assertCount(2, $matches[1]);
        $this->assertEquals($page1->name, $matches[1][0]);
        $this->assertEquals($page2->name, $matches[1][1]);
    }
}
