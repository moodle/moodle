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

namespace filter_glossary;

/**
 * Unit tests.
 *
 * @package filter_glossary
 * @category test
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_glossary\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    public function test_link_to_entry_with_alias(): void {
        global $CFG;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $normal = $generator->create_content(
            $glossary,
            ['concept' => 'entry name'],
            ['first alias', 'second alias']
        );

        // Format text with all three entries in HTML.
        $html = '<p>First we have entry name, then we have it twp aliases first alias and second alias.</p>';
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~eid=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 3 glossary links.
        $this->assertEquals(3, count($matches[1]));
        $this->assertEquals($normal->id, $matches[1][0]);
        $this->assertEquals($normal->id, $matches[1][1]);
        $this->assertEquals($normal->id, $matches[1][2]);

        // Check text of title attribute.
        $this->assertEquals($glossary->name . ': entry name', $matches[2][0]);
        $this->assertEquals($glossary->name . ': first alias', $matches[2][1]);
        $this->assertEquals($glossary->name . ': second alias', $matches[2][2]);
    }

    public function test_longest_link_used(): void {
        global $CFG;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $shorter = $generator->create_content($glossary, ['concept' => 'Tim']);
        $longer = $generator->create_content($glossary, ['concept' => 'Time']);

        // Format text with all three entries in HTML.
        $html = '<p>Time will tell</p>';
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~eid=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 1 glossary link to Time, not Tim.
        $this->assertEquals(1, count($matches[1]));
        $this->assertEquals($longer->id, $matches[1][0]);

        // Check text of title attribute.
        $this->assertEquals($glossary->name . ': Time', $matches[2][0]);
    }

    public function test_link_to_category(): void {
        global $CFG;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        /** @var \mod_glossary_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $category = $generator->create_category($glossary, ['name' => 'My category', 'usedynalink' => 1]);

        // Format text with all three entries in HTML.
        $html = '<p>This is My category you know.</p>';
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~hook=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 1 glossary link.
        $this->assertEquals(1, count($matches[1]));
        $this->assertEquals($category->id, $matches[1][0]);
        $this->assertEquals($glossary->name . ': Category My category', $matches[2][0]);
    }

    /**
     * Test ampersands.
     */
    public function test_ampersands(): void {
        global $CFG;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        /** @var \mod_glossary_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $normal = $generator->create_content($glossary, ['concept' => 'normal']);
        $amp1 = $generator->create_content($glossary, ['concept' => 'A&B']);
        $amp2 = $generator->create_content($glossary, ['concept' => 'C&amp;D']);

        // Format text with all three entries in HTML.
        $html = '<p>A&amp;B C&amp;D normal</p>';
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~eid=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 3 glossary links.
        $this->assertEquals(3, count($matches[1]));
        $this->assertEquals($amp1->id, $matches[1][0]);
        $this->assertEquals($amp2->id, $matches[1][1]);
        $this->assertEquals($normal->id, $matches[1][2]);

        // Check text and escaping of title attribute.
        $this->assertEquals($glossary->name . ': A&amp;B', $matches[2][0]);
        $this->assertEquals($glossary->name . ': C&amp;D', $matches[2][1]);
        $this->assertEquals($glossary->name . ': normal', $matches[2][2]);
    }

    /**
     * Test brackets.
     */
    public function test_brackets(): void {
        global $CFG;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        /** @var \mod_glossary_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $simple = $generator->create_content($glossary, ['concept' => 'simple']);
        $withbrackets = $generator->create_content($glossary, ['concept' => 'more complex (perhaps)']);
        $test2 = $generator->create_content($glossary, ['concept' => 'Test (2)']);

        // Format text with all three entries in HTML.
        $html = '<p>Some thigns are simple. Others are more complex (perhaps). Test (2).</p>';
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~eid=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 3 glossary links.
        $this->assertEquals(3, count($matches[1]));
        $this->assertEquals($simple->id, $matches[1][0]);
        $this->assertEquals($withbrackets->id, $matches[1][1]);
        $this->assertEquals($test2->id, $matches[1][2]);

        // Check text and escaping of title attribute.
        $this->assertEquals($glossary->name . ': simple', $matches[2][0]);
        $this->assertEquals($glossary->name . ': more complex (perhaps)', $matches[2][1]);
        $this->assertEquals($glossary->name . ': Test (2)', $matches[2][2]);
    }

    public function test_exclude_excludes_link_to_entry_with_alias(): void {
        global $CFG, $GLOSSARY_EXCLUDEENTRY;

        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $tobeexcluded = $generator->create_content(
            $glossary,
            ['concept' => 'entry name'],
            ['first alias', 'second alias']
        );
        $normal = $generator->create_content($glossary, ['concept' => 'other entry']);

        // Format text with all three entries in HTML.
        $html = '<p>First we have entry name, then we have it twp aliases first alias and second alias. ' .
                'In this case, those should not be linked, but this other entry should be.</p>';
        $GLOSSARY_EXCLUDEENTRY = $tobeexcluded->id;
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);
        $GLOSSARY_EXCLUDEENTRY = null;

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~eid=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 1 glossary links.
        $this->assertEquals(1, count($matches[1]));
        $this->assertEquals($normal->id, $matches[1][0]);
        $this->assertEquals($glossary->name . ': other entry', $matches[2][0]);
    }

    public function test_exclude_does_not_exclude_categories(): void {
        global $CFG, $GLOSSARY_EXCLUDEENTRY;
        $this->resetAfterTest(true);

        // Enable glossary filter at top level.
        filter_set_global_state('glossary', TEXTFILTER_ON);
        $CFG->glossary_linkentries = 1;

        // Create a test course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a glossary.
        $glossary = $this->getDataGenerator()->create_module(
            'glossary',
            ['course' => $course->id, 'mainglossary' => 1]
        );

        // Create two entries with ampersands and one normal entry.
        /** @var \mod_glossary_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $category = $generator->create_category($glossary, ['name' => 'My category', 'usedynalink' => 1]);

        // Format text with all three entries in HTML.
        $html = '<p>This is My category you know.</p>';
        $GLOSSARY_EXCLUDEENTRY = $category->id;
        $filtered = format_text($html, FORMAT_HTML, ['context' => $context]);
        $GLOSSARY_EXCLUDEENTRY = null;

        // Find all the glossary links in the result.
        $matches = [];
        preg_match_all('~hook=([0-9]+).*?title="(.*?)"~', $filtered, $matches);

        // There should be 1 glossary link.
        $this->assertEquals(1, count($matches[1]));
        $this->assertEquals($category->id, $matches[1][0]);
        $this->assertEquals($glossary->name . ': Category My category', $matches[2][0]);
    }
}
