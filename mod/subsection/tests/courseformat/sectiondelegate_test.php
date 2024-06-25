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

namespace mod_subsection\courseformat;

/**
 * Subsection delegated section tests.
 *
 * @package    mod_subsection
 * @copyright  2024 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_subsection\courseformat\sectiondelegate
 * @coversDefaultClass \mod_subsection\courseformat\sectiondelegate
 */
final class sectiondelegate_test extends \advanced_testcase {

    /**
     * Test has_delegate_class().
     *
     * @covers ::has_delegate_class
     */
    public function test_has_delegate_class(): void {
        $this->assertTrue(sectiondelegate::has_delegate_class('mod_subsection'));
    }

    /**
     * Test get_section_action_menu().
     *
     * @covers ::get_section_action_menu
     */
    public function test_get_section_action_menu(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);

        $modinfo = get_fast_modinfo($course->id);
        $sectioninfos = $modinfo->get_section_info_all();
        // Get the section info for the delegated section.
        $sectioninfo = $sectioninfos[2];
        $delegated = sectiondelegate::instance($sectioninfo);
        $format = course_get_format($course);

        $outputclass = $format->get_output_classname('content\\section\\controlmenu');
        $controlmenu = new $outputclass($format, $sectioninfo);
        $renderer = $PAGE->get_renderer('format_' . $course->format);

        // The default section menu should be different for the delegated section menu.
        $result = $delegated->get_section_action_menu($format, $controlmenu, $renderer);
        foreach ($result->get_secondary_actions() as $secondaryaction) {
            // Highlight and Permalink are only present in section menu (not module), so they shouldn't be find in the result.
            $this->assertNotEquals(get_string('highlight'), $secondaryaction->text);
            $this->assertNotEquals(get_string('sectionlink', 'course'), $secondaryaction->text);
        }
    }
}
