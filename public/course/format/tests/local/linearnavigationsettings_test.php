<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_courseformat\local;

/**
 * Tests for the linear navigation settings class.
 *
 * @package    core_courseformat
 * @copyright  2026 Sara Arjona <sara@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(linearnavigationsettings::class)]
final class linearnavigationsettings_test extends \advanced_testcase {
    /**
     * Test show_navigation_footer.
     *
     * @param string $format The course format to use.
     * @param int $enablelinearnav The value of the enablelinearnav setting.
     * @param bool $hasstickyfooter Whether the page already has a sticky footer.
     * @param bool $shownavigationfooter Whether the navigation footer should be shown.
     * @param bool $hascm Whether the page is on an activity page.
     * @param bool $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('show_navigation_footer_provider')]
    public function test_show_navigation_footer(
        string $format,
        int $enablelinearnav,
        bool $hasstickyfooter,
        bool $shownavigationfooter,
        bool $hascm,
        bool $expected,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'format' => $format,
            'enablelinearnav' => $enablelinearnav,
        ]);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);

        $moodlepage = new \moodle_page();
        $moodlepage->set_course($course);
        if ($hascm) {
            $cm = get_coursemodule_from_id('page', $page->cmid);
            $moodlepage->set_cm($cm, $course);
        }
        $moodlepage->set_has_sticky_footer($hasstickyfooter);
        $moodlepage->set_show_navigation_footer($shownavigationfooter);

        $this->assertSame($expected, linearnavigationsettings::show_navigation_footer($moodlepage));
    }

    /**
     * Data provider for {@see test_show_navigation_footer}.
     *
     * @return array
     */
    public static function show_navigation_footer_provider(): array {
        return [
            'Supported format with linear navigation enabled' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => true,
            ],
            'Supported format with linear navigation disabled' => [
                'format' => 'topics',
                'enablelinearnav' => 0,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => false,
            ],
            'Unsupported format' => [
                'format' => 'singleactivity',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => false,
            ],
            'Not on an activity page' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => false,
                'expected' => false,
            ],
            'Page already has a sticky footer' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => true,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => false,
            ],
            'Navigation footer is suppressed' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => false,
                'hascm' => true,
                'expected' => false,
            ],
        ];
    }
}
