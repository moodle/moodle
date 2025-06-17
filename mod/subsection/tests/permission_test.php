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

declare(strict_types=1);

namespace mod_subsection;

use advanced_testcase;
use context_course;

/**
 * Unit tests for the subsection permission class
 *
 * @package     mod_subsection
 * @covers      \mod_subsection\permission
 * @copyright   2024 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class permission_test extends advanced_testcase {

    /**
     * Test that viewing reports list observes capability to do so
     *
     * @param bool $ismoddisabled
     * @param bool $missingcapability
     * @param bool $isdelegated
     * @param string $format
     * @param bool $expected
     *
     * @dataProvider can_add_subsection_provider
     */
    public function test_can_add_subsection(
        bool $ismoddisabled,
        bool $missingcapability,
        bool $isdelegated,
        string $format,
        bool $expected
    ): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => $format, 'numsections' => 5]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $courseformat = course_get_format($course->id);
        $targetsection = $courseformat->get_modinfo()->get_section_info(5);

        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', (int)!$ismoddisabled);

        if ($missingcapability) {
            $userrole = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
            assign_capability('mod/subsection:addinstance',  CAP_PROHIBIT, $userrole, context_course::instance($course->id));
        }

        if ($isdelegated) {
            $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);
            $targetsection = $courseformat->get_modinfo()->get_section_info(6);
        }

        $this->setUser($user);
        $this->assertEquals($expected, permission::can_add_subsection($targetsection, (int)$user->id));
    }

    /**
     * Data provider for {@see self::test_can_add_subsection}
     *
     * @return array[]
     */
    public static function can_add_subsection_provider(): array {
        return [
            'Plugin disabled' => [
                'ismoddisabled' => true,
                'missingcapability' => false,
                'isdelegated' => false,
                'format' => 'topics',
                'expected' => false,
            ],
            'User without capability' => [
                'ismoddisabled' => false,
                'missingcapability' => true,
                'isdelegated' => false,
                'format' => 'topics',
                'expected' => false,
            ],
            'Target section is a delegated section' => [
                'ismoddisabled' => false,
                'missingcapability' => false,
                'isdelegated' => true,
                'format' => 'topics',
                'expected' => false,
            ],
            'Single activity supports subsections too' => [
                'ismoddisabled' => false,
                'missingcapability' => false,
                'isdelegated' => false,
                'format' => 'singleactivity',
                'expected' => true,
            ],
            'Plugin enabled, with capability, not inside a delegated section' => [
                'ismoddisabled' => false,
                'missingcapability' => false,
                'isdelegated' => false,
                'format' => 'topics',
                'expected' => true,
            ],
        ];
    }

    /**
     * Test that subsection cannot be added when the course format does not support components.
     *
     * @return void
     */
    public function test_can_add_subsection_unsupported_components(
    ): void {
        global $SITE;

        $this->resetAfterTest();

        course_create_sections_if_missing($SITE, [0, 1]);
        $courseformat = course_get_format($SITE);
        $targetsection = $courseformat->get_modinfo()->get_section_info(0);

        $user = $this->getDataGenerator()->create_and_enrol($SITE, 'editingteacher');

        $this->setUser($user);
        $this->assertEquals(false, permission::can_add_subsection($targetsection, (int)$user->id));
    }


}
