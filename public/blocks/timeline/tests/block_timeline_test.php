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
 * Unit tests for the block_timeline block class.
 *
 * @package    block_timeline
 * @category   test
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_timeline;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the block_timeline block class.
 *
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\block_timeline::class)]
final class block_timeline_test extends \advanced_testcase {
    /**
     * @var \stdClass enrolled test user, created once per test via setUp.
     */
    private $user;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        global $CFG;
        // The get_content() requires these constants; loaded here so tests can reference
        // them too, mirroring the require_once already present in get_content() itself.
        require_once($CFG->dirroot . '/blocks/timeline/lib.php');

        $this->resetAfterTest();
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);
    }

    /**
     * Extract and decode the data-react-props JSON from get_content()'s mount markup.
     *
     * @param \stdClass $content the object returned by block_timeline::get_content().
     * @return \stdClass decoded props.
     */
    private function decode_props(\stdClass $content): \stdClass {
        $this->assertMatchesRegularExpression(
            '#data-react-component="@moodle/lms/block_timeline/Timeline"#',
            $content->text
        );
        preg_match('/data-react-props="([^"]*)"/', $content->text, $matches);
        $this->assertNotEmpty($matches, 'data-react-props attribute not found in block content');
        return json_decode(htmlspecialchars_decode($matches[1]));
    }

    /**
     * get_content() must emit a React mount point, not rendered HTML from a renderer.
     */
    public function test_get_content_emits_react_mount_point(): void {
        $block = \block_instance('timeline');
        $block->init();

        $content = $block->get_content();

        $this->decode_props($content);
        $this->assertSame('', $content->footer);
    }

    /**
     * hasenrolledcourses must reflect whether the current user has any course enrolments.
     */
    public function test_get_content_reports_hasenrolledcourses_true_when_enrolled(): void {
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($this->user->id, $course->id, 'student');

        $block = \block_instance('timeline');
        $block->init();
        $props = $this->decode_props($block->get_content());

        $this->assertTrue($props->hasenrolledcourses);
    }

    /**
     * hasenrolledcourses must be false for a user with no course enrolments.
     */
    public function test_get_content_reports_hasenrolledcourses_false_when_not_enrolled(): void {
        $block = \block_instance('timeline');
        $block->init();
        $props = $this->decode_props($block->get_content());

        $this->assertFalse($props->hasenrolledcourses);
    }

    /**
     * With no saved user preferences, props must fall back to the block's documented defaults.
     */
    public function test_get_content_uses_default_preferences_when_unset(): void {
        $block = \block_instance('timeline');
        $block->init();
        $props = $this->decode_props($block->get_content());

        $this->assertEquals(BLOCK_TIMELINE_FILTER_BY_30_DAYS, $props->filter);
        $this->assertEquals(BLOCK_TIMELINE_SORT_BY_DATES, $props->order);
        $this->assertEquals(BLOCK_TIMELINE_ACTIVITIES_LIMIT_DEFAULT, $props->limit);
    }

    /**
     * Saved user preferences must be read through into the seeded props, including the
     * int-cast on the limit preference (stored as a string in the user_preferences table).
     */
    public function test_get_content_uses_saved_user_preferences(): void {
        set_user_preference('block_timeline_user_filter_preference', BLOCK_TIMELINE_FILTER_BY_OVERDUE, $this->user);
        set_user_preference('block_timeline_user_sort_preference', BLOCK_TIMELINE_SORT_BY_COURSES, $this->user);
        set_user_preference('block_timeline_user_limit_preference', 15, $this->user);

        $block = \block_instance('timeline');
        $block->init();
        $props = $this->decode_props($block->get_content());

        $this->assertEquals(BLOCK_TIMELINE_FILTER_BY_OVERDUE, $props->filter);
        $this->assertEquals(BLOCK_TIMELINE_SORT_BY_COURSES, $props->order);
        $this->assertSame(15, $props->limit);
    }

    /**
     * get_content() must cache its result on $this->content rather than rebuilding on every call.
     */
    public function test_get_content_caches_result_on_repeated_calls(): void {
        $block = \block_instance('timeline');
        $block->init();

        $first = $block->get_content();
        $second = $block->get_content();

        $this->assertSame($first, $second);
    }

    /**
     * The image URLs seeded into props are embedded in JSON, then in an HTML attribute —
     * they must not be pre-escaped for raw HTML (out(true), the default), or a URL with
     * more than one query parameter ends up double-escaped (&amp;amp;) once the browser's
     * single level of HTML-entity decoding hands the JSON off to be parsed.
     */
    public function test_get_content_does_not_double_escape_image_urls_with_query_params(): void {
        global $CFG;
        $CFG->slasharguments = 0;

        $block = \block_instance('timeline');
        $block->init();
        $props = $this->decode_props($block->get_content());

        $this->assertStringContainsString('&', $props->nocoursesurl);
        $this->assertStringNotContainsString('&amp;', $props->nocoursesurl);
        $this->assertStringContainsString('&', $props->noeventsurl);
        $this->assertStringNotContainsString('&amp;', $props->noeventsurl);
    }
}
