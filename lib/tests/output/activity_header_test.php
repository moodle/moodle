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

namespace core\output;

/**
 * Unit tests for activity header
 *
 * @package   core
 * @category  test
 * @coversDefaultClass \core\output\activity_header
 * @copyright 2021 Peter
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class activity_header_test extends \advanced_testcase {

    /**
     * Test the title setter
     *
     * @dataProvider set_title_provider
     * @param string $value
     * @param string $expected
     * @covers ::set_title
     */
    public function test_set_title(string $value, string $expected): void {
        global $PAGE, $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ]);
        $this->setAdminUser();

        $cm = $DB->get_record('course_modules', ['id' => $assign->cmid]);
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($assign);

        $header = $PAGE->activityheader;
        $header->set_title($value);
        $data = $header->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals($expected, $data['title']);
    }

    /**
     * Provider for the test_set_title unit test.
     * @return array
     */
    public static function set_title_provider(): array {
        return [
            "Set the title with a plain text" => [
                "Activity title", "Activity title"
            ],
            "Set the title with a string with standard header tags" => [
                "<h2>Activity title</h2>", "Activity title"
            ],
            "Set the title with a string with multiple header content" => [
                "<h2 id='heading'>Activity title</h2><h2>Header 2</h2>", "Activity title</h2><h2>Header 2"
            ],
        ];
    }

    /**
     * Test setting multiple attributes
     *
     * @covers ::set_attrs
     */
    public function test_set_attrs(): void {
        global $DB, $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ]);

        $cm = $DB->get_record('course_modules', ['id' => $assign->cmid]);
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($assign);

        $PAGE->activityheader->set_attrs([
            'hidecompletion' => true,
            'additionalnavitems' => new \url_select([]),
            'hideoverflow' => true,
            'title' => 'My title',
            'description' => 'My description',
        ]);

        $renderer = $PAGE->get_renderer('core');
        $export = $PAGE->activityheader->export_for_template($renderer);

        $this->assertEquals('My title', $export['title']);
        $this->assertEquals('My description', $export['description']);
        $this->assertEmpty($export['completion']); // Because hidecompletion = true.
        $this->assertEmpty($export['additional_items']); // Because hideoverflow = true.
    }

    /**
     * Test calling set_attrs with an invalid variable name
     *
     * @covers ::set_attrs
     */
    public function test_set_attrs_invalid_variable(): void {
        global $PAGE;

        $PAGE->activityheader->set_attrs(['unknown' => true]);
        $this->assertDebuggingCalledCount(1, ['Invalid class member variable: unknown']);
    }

    /**
     * Data provider for {@see test_get_heading_level()}.
     *
     * @return array[]
     */
    public static function get_heading_level_provider(): array {
        return [
            'Title not allowed' => [false, '', 2],
            'Title allowed, no title' => [true, '', 2],
            'Title allowed, empty string title' => [true, ' ', 2],
            'Title allowed, non-empty string title' => [true, 'Cool', 3],
        ];
    }

    /**
     * Test the heading level getter
     *
     * @dataProvider get_heading_level_provider
     * @covers ::get_heading_level
     * @param bool $allowtitle Whether the title is allowed.
     * @param string $title The activity heading.
     * @param int $expectedheadinglevel The expected heading level.
     */
    public function test_get_heading_level(bool $allowtitle, string $title, int $expectedheadinglevel): void {
        $activityheaderstub = $this->getMockBuilder(activity_header::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['is_title_allowed'])
            ->getMock();
        $activityheaderstub->method('is_title_allowed')->willReturn($allowtitle);
        $activityheaderstub->set_title($title);
        $this->assertEquals($expectedheadinglevel, $activityheaderstub->get_heading_level());
    }

    /**
     * Tests that is_title_allowed returns correctly based on the theme default and current layout options.
     *
     * The current layout has precedence, if the notitle option is set, otherwise the theme default is used if set.
     *
     * @param array $themeoptions The activityheader options array set in the theme.
     * @param array $layoutoptions The activitityheader options array set in the layout.
     * @param bool $allowed The expected return value of is_title_allowed.
     * @covers ::is_title_allowed
     * @dataProvider get_title_options
     * @return void
     */
    public function test_is_title_allowed(array $themeoptions, array $layoutoptions, bool $allowed): void {
        $themeconfig = $this->getMockBuilder(\theme_config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $themeconfig->activityheaderconfig = $themeoptions;
        $page = $this->getMockBuilder(\moodle_page::class)
            ->getMock();
        // Mocking the magic_get_layout_options() and magic_get_theme() methods directly doesn't work,
        // so mock the whole magic __get() method and just return the test values for those properties.
        $page->expects($this->any())->method('__get')->willReturnCallback(fn($name) => match($name) {
            'layout_options' => ['activityheader' => $layoutoptions],
            'theme' => $themeconfig,
            default => null
        });
        $user = new \stdClass();

        $activityheader = new activity_header($page, $user);
        $this->assertEquals($allowed, $activityheader->is_title_allowed());
    }

    /**
     * Return scenarios for test_is_title_allowed.
     *
     * Test each combination of the 'notitle' option being unset, true and false in each of the theme and layout options.
     *
     * @return array[]
     */
    public static function get_title_options(): array {
        return [
            'Undefined in theme, undefined in layout' => [[], [], true],
            'Undefined in theme, disallowed in layout' => [[], ['notitle' => true], false],
            'Undefined in theme, allowed in layout' => [[], ['notitle' => false], true],
            'Disallowed in theme, undefined in layout' => [['notitle' => true], [], false],
            'Disallowed in theme, disallowed in layout' => [['notitle' => true], ['notitle' => true], false],
            'Disallowed in theme, allowed in layout' => [['notitle' => true], ['notitle' => false], true],
            'Allowed in theme, undefined in layout' => [['notitle' => false], [], true],
            'Allowed in theme, disallowed in layout' => [['notitle' => false], ['notitle' => true], false],
            'Allowed in theme, allowed in layout' => [['notitle' => false], ['notitle' => false], true],
        ];
    }
}
