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
     * @param array $cmoptions Options to pass to the module generator.
     * @param string $user The user role to use for the test. Default: student. Use 'admin' to use the admin user.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('show_navigation_footer_provider')]
    public function test_show_navigation_footer(
        string $format,
        int $enablelinearnav,
        bool $hasstickyfooter,
        bool $shownavigationfooter,
        bool $hascm,
        bool $expected,
        array $cmoptions = [],
        string $user = 'student',
    ): void {
        $this->resetAfterTest();
        if (array_key_exists('visibleoncoursepage', $cmoptions)) {
            set_config('allowstealth', 1);
        }
        $sectionhidden = $cmoptions['sectionhidden'] ?? false;
        unset($cmoptions['sectionhidden']);

        if ($user === 'admin') {
            $this->setAdminUser();
        } else {
            $this->setUser($this->getDataGenerator()->create_user());
        }

        $course = $this->getDataGenerator()->create_course([
            'format' => $format,
            'enablelinearnav' => $enablelinearnav,
        ]);

        $moodlepage = new \moodle_page();
        $moodlepage->set_course($course);
        if ($sectionhidden) {
            $sectioninfo = get_fast_modinfo($course->id)->get_section_info(0);
            \core_courseformat\formatactions::section($course->id)->set_visibility($sectioninfo, false);
        }
        $module = $this->getDataGenerator()->create_module('page', array_merge(['course' => $course->id], $cmoptions));
        if ($hascm) {
            $cm = get_coursemodule_from_id('page', $module->cmid);
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
            'Stealth module' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => false,
                'cmoptions' => ['visibleoncoursepage' => false],
            ],
            'Section hidden, visible module' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => false,
                'cmoptions' => ['visible' => true, 'sectionhidden' => true],
            ],
            'Admin: Stealth module' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => true,
                'cmoptions' => ['visibleoncoursepage' => false],
                'user' => 'admin',
            ],
            'Admin: Section hidden, visible module' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'hasstickyfooter' => false,
                'shownavigationfooter' => true,
                'hascm' => true,
                'expected' => true,
                'cmoptions' => ['visible' => true, 'sectionhidden' => true],
                'user' => 'admin',
            ],
        ];
    }

    /**
     * Test is_linear_navigation_enabled.
     *
     * @param string $format The course format to use.
     * @param int $enablelinearnav The value of the enablelinearnav setting.
     * @param bool $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('is_linear_navigation_enabled_provider')]
    public function test_is_linear_navigation_enabled(
        string $format,
        int $enablelinearnav,
        bool $expected,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'format' => $format,
            'enablelinearnav' => $enablelinearnav,
        ]);

        $this->assertSame($expected, linearnavigationsettings::is_linear_navigation_enabled($course));
    }

    /**
     * Data provider for {@see test_is_linear_navigation_enabled}.
     *
     * @return array
     */
    public static function is_linear_navigation_enabled_provider(): array {
        return [
            'Supported format with linear navigation enabled' => [
                'format' => 'topics',
                'enablelinearnav' => 1,
                'expected' => true,
            ],
            'Supported format with linear navigation disabled' => [
                'format' => 'topics',
                'enablelinearnav' => 0,
                'expected' => false,
            ],
            'Unsupported format with linear navigation enabled' => [
                'format' => 'singleactivity',
                'enablelinearnav' => 1,
                'expected' => false,
            ],
        ];
    }
}
