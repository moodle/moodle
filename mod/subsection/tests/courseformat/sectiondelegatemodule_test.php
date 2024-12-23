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

use mod_subsection\courseformat\sectiondelegate as testsectiondelegatemodule;
use section_info;
use cm_info;
use stdClass;

/**
 * Section delegate module tests.
 *
 * @package    mod_subsection
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_courseformat\sectiondelegatemodule
 * @coversDefaultClass \core_courseformat\sectiondelegatemodule
 */
final class sectiondelegatemodule_test extends \advanced_testcase {

    /**
     * Test get_parent_section.
     *
     * @covers ::get_parent_section
     */
    public function test_get_parent_section(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);
        $module = $this->getDataGenerator()->create_module('subsection', (object)['course' => $course->id, 'section' => 2]);

        // Get the section info for the delegated section.
        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);

        /** @var testsectiondelegatemodule */
        $delegated = sectiondelegate::instance($sectioninfo);

        $parentsectioninfo = $delegated->get_parent_section();

        $this->assertInstanceOf(section_info::class, $parentsectioninfo);
        $this->assertEquals(2, $parentsectioninfo->sectionnum);
    }

    /**
     * Test get_cm.
     *
     * @covers ::get_cm
     */
    public function test_get_cm(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        $module = $this->getDataGenerator()->create_module('subsection', (object)['course' => $course->id, 'section' => 1]);

        // Get the section info for the delegated section.
        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);

        /** @var testsectiondelegatemodule */
        $delegated = sectiondelegate::instance($sectioninfo);

        $delegatedsectioncm = $delegated->get_cm();

        $this->assertInstanceOf(cm_info::class, $delegatedsectioncm);
        $this->assertEquals($module->id, $delegatedsectioncm->instance);
    }

    /**
     * Test get_course.
     *
     * @covers ::get_course
     */
    public function test_get_course(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);
        $module = $this->getDataGenerator()->create_module('subsection', (object)['course' => $course->id, 'section' => 1]);

        // Get the section info for the delegated section.
        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);

        /** @var testsectiondelegatemodule */
        $delegated = sectiondelegate::instance($sectioninfo);

        $delegatedsectioncourse = $delegated->get_course();

        $this->assertInstanceOf(stdClass::class, $delegatedsectioncourse);
        $this->assertEquals($course->id, $delegatedsectioncourse->id);
    }

    public function test_instance_plugin_disabled(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);
        $module = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 2]
        );

        // Get the section info for the delegated section.
        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);

        /** @var testsectiondelegatemodule $delegated */
        $delegated = sectiondelegate::instance($sectioninfo);
        $this->assertTrue($delegated->is_enabled());

        // Disabling the plugin should disable the delegate.
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', 0);
        rebuild_course_cache($course->id, true);

        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);

        /** @var testsectiondelegatemodule $delegated */
        $delegated = sectiondelegate::instance($sectioninfo);
        // Delegated from a disabled plugin are considered orphaned, not delegated.
        $this->assertNull($delegated);

        // Section delegate should not be created directly but we do it
        // here to validate the is_enabled() method neverthless.
        $delegatedinstance = new sectiondelegate($sectioninfo);
        $this->assertFalse($delegatedinstance->is_enabled());
    }
}
