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

namespace core_courseformat;

use test_component\courseformat\sectiondelegate as testsectiondelegate;

/**
 * Section delegate tests.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_courseformat\sectiondelegate
 * @coversDefaultClass \core_courseformat\sectiondelegate
 */
class sectiondelegate_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/tests/fixtures/sectiondelegatetest.php');
    }

    /**
     * Test that the instance method returns the correct class.
     * @covers ::instance
     */
    public function test_instance(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 3]);

        // Section 2 has an existing delegate class.
        course_update_section(
            $course,
            $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]),
            [
                'component' => 'test_component',
                'itemid' => 1,
            ]
        );

        // Section 3 has a missing delegate class.
        course_update_section(
            $course,
            $DB->get_record('course_sections', ['course' => $course->id, 'section' => 3]),
            [
                'component' => 'missing_component',
                'itemid' => 1,
            ]
        );

        $modinfo = get_fast_modinfo($course->id);
        $sectioninfos = $modinfo->get_section_info_all();

        $this->assertNull(sectiondelegate::instance($sectioninfos[1]));
        $this->assertInstanceOf('\test_component\courseformat\sectiondelegate', sectiondelegate::instance($sectioninfos[2]));
        $this->assertNull(sectiondelegate::instance($sectioninfos[3]));
    }

    /**
     * Test has_delegate_class().
     *
     * @covers ::has_delegate_class
     */
    public function test_has_delegate_class(): void {
        $this->assertFalse(sectiondelegate::has_delegate_class('missing_component'));
        $this->assertFalse(sectiondelegate::has_delegate_class('mod_label'));
        $this->assertTrue(sectiondelegate::has_delegate_class('test_component'));
    }

    /**
     * Test get_section_action_menu().
     *
     * @covers ::get_section_action_menu
     */
    public function test_get_section_action_menu(): void {
        global $DB, $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);

        $sectioninfo = formatactions::section($course)->create_delegated('test_component', 1);

        /** @var testsectiondelegate */
        $delegated = $sectioninfo->get_component_instance();

        $format = course_get_format($course);

        $outputclass = $format->get_output_classname('content\\section\\controlmenu');
        /** @var \core_courseformat\output\local\content\section\controlmenu */
        $controlmenu = new $outputclass($format, $sectioninfo);
        $renderer = $PAGE->get_renderer('format_' . $course->format);
        $sectionmenu = $controlmenu->get_action_menu($renderer);

        // When the delegate class returns the same action menu, calculated from the given $controlmenu.
        $result = $delegated->get_section_action_menu($format, $controlmenu, $renderer);
        // The $result and $sectionmenu are the same but can't be compared directly because they have different ids.
        $this->assertEquals(
            count($result->get_primary_actions()),
            count($sectionmenu->get_primary_actions()),
        );
        $this->assertEquals(
            count($result->get_secondary_actions()),
            count($sectionmenu->get_secondary_actions())
        );
        $this->assertEquals(
            $result->get_secondary_actions()[0]->url,
            $sectionmenu->get_secondary_actions()[0]->url
        );

        // When the delegated class returns an empty action menu.
        $delegated->set_section_action_menu(testsectiondelegate::MENUEMPTY);
        $result = $delegated->get_section_action_menu($format, $controlmenu, $renderer);
        // The $result and $sectionmenu are different.
        $this->assertNotEquals(
            count($result->get_secondary_actions()),
            count($sectionmenu->get_secondary_actions())
        );

        // When the delegated class return a null action menu.
        $delegated->set_section_action_menu(null);
        $result = $delegated->get_section_action_menu($format, $controlmenu, $renderer);
        $this->assertNull($result);
    }
}
