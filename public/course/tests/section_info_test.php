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

namespace core_course;

use core\context\course as context_course;
use core\plugin_manager;
use core_courseformat\formatactions;

/**
 * Tests for \core_course\section_info.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPunit\Framework\Attributes\CoversClass(\core\section_info::class)]
final class section_info_test extends \advanced_testcase {
    public function test_section_info_properties(): void {
        global $DB, $CFG;

        $this->resetAfterTest();
        set_config('enableavailability', 1);
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(
            [
                'format' => 'topics',
                'numsections' => 3,
                'enablecompletion' => 1,
                'groupmode' => SEPARATEGROUPS,
                'forcegroupmode' => 0,
            ],
            ['createsections' => true],
        );
        $coursecontext = context_course::instance($course->id);
        $prereqforum = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id],
            ['completion' => 1],
        );

        // Add availability conditions.
        $availability = '{"op":"&","showc":[true,true,true],"c":[' .
                '{"type":"completion","cm":' . $prereqforum->cmid . ',"e":"' .
                    COMPLETION_COMPLETE . '"},' .
                '{"type":"grade","id":666,"min":0.4},' .
                '{"type":"profile","op":"contains","sf":"email","v":"test"}' .
                ']}';
        $DB->set_field(
            'course_sections',
            'availability',
            $availability,
            ['course' => $course->id, 'section' => 2],
        );
        rebuild_course_cache($course->id, true);
        $sectiondb = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]);

        // Create and enrol a student.
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Get modinfo.
        $modinfo = get_fast_modinfo($course->id);
        $si = $modinfo->get_section_info(2);

        $this->assertEquals($sectiondb->id, $si->id);
        $this->assertEquals($sectiondb->course, $si->course);
        $this->assertEquals($sectiondb->section, $si->sectionnum);
        $this->assertEquals($sectiondb->name, $si->name);
        $this->assertEquals($sectiondb->visible, $si->visible);
        $this->assertEquals($sectiondb->summary, $si->summary);
        $this->assertEquals($sectiondb->summaryformat, $si->summaryformat);
        $this->assertEquals($sectiondb->sequence, $si->sequence); // Since this section does not contain invalid modules.
        $this->assertEquals($availability, $si->availability);

        // Dynamic fields, just test that they can be retrieved (must be carefully tested in each activity type).
        $this->assertEquals(0, $si->available);
        $this->assertNotEmpty($si->availableinfo); // Lists all unmet availability conditions.
        $this->assertEquals(0, $si->uservisible);
    }

    /**
     * Test for get_component_instance.
     */
    public function test_get_component_instance(): void {
        global $DB;
        $this->resetAfterTest();
        $this->load_fixture('core', 'sectiondelegatetest.php');

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        course_update_section(
            $course,
            $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]),
            [
                'component' => 'test_component',
                'itemid' => 1,
            ],
        );

        $modinfo = get_fast_modinfo($course->id);
        $sectioninfos = $modinfo->get_section_info_all();

        $this->assertNull($sectioninfos[1]->get_component_instance());
        $this->assertNull($sectioninfos[1]->component);
        $this->assertNull($sectioninfos[1]->itemid);

        $this->assertInstanceOf(\core_courseformat\sectiondelegate::class, $sectioninfos[2]->get_component_instance());
        $this->assertInstanceOf(\test_component\courseformat\sectiondelegate::class, $sectioninfos[2]->get_component_instance());
        $this->assertEquals('test_component', $sectioninfos[2]->component);
        $this->assertEquals(1, $sectioninfos[2]->itemid);
    }

    /**
     * Test for section_info is_delegated.
     */
    public function test_is_delegated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);

        formatactions::section($course)->create_delegated('mod_label', 0);

        $modinfo = get_fast_modinfo($course->id);
        $sectioninfos = $modinfo->get_section_info_all();

        $this->assertFalse($sectioninfos[1]->is_delegated());
        $this->assertTrue($sectioninfos[2]->is_delegated());
    }

    /**
     * Test get_uservisible method when the section is delegated.
     *
     * @param string $role The role to assign to the user.
     * @param bool $parentvisible The visibility of the parent section.
     * @param bool $delegatedvisible The visibility of the delegated section.
     * @param bool $expected The expected visibility of the delegated section.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_uservisible_delegate')]
    public function test_get_uservisible_delegate(
        string $role,
        bool $parentvisible,
        bool $delegatedvisible,
        bool $expected,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course], ['section' => 1]);

        $student = $this->getDataGenerator()->create_and_enrol($course, $role);

        $modinfo = get_fast_modinfo($course);

        formatactions::section($course)->update(
            $modinfo->get_section_info(1),
            ['visible' => $parentvisible],
        );

        formatactions::cm($course)->set_visibility(
            $subsection->cmid,
            $delegatedvisible,
        );

        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        $delegatedsection = $modinfo->get_cm($subsection->cmid)->get_delegated_section_info();

        // The get_uservisible is a magic getter.
        $this->assertEquals($expected, $delegatedsection->uservisible);
    }

    /**
     * Data provider for test_get_uservisible_delegate.
     *
     * @return array
     */
    public static function data_provider_get_uservisible_delegate(): array {
        return [
            'Student on a visible subsection inside a visible parent' => [
                'role' => 'student',
                'parentvisible' => true,
                'delegatedvisible' => true,
                'expected' => true,
            ],
            'Student on a hidden subsection inside a visible parent' => [
                'role' => 'student',
                'parentvisible' => true,
                'delegatedvisible' => false,
                'expected' => false,
            ],
            'Student on a visible subsection inside a hidden parent' => [
                'role' => 'student',
                'parentvisible' => false,
                'delegatedvisible' => true,
                'expected' => false,
            ],
            'Student on a hidden subsection inside a hidden parent' => [
                'role' => 'student',
                'parentvisible' => false,
                'delegatedvisible' => false,
                'expected' => false,
            ],
            'Teacher on a visible subsection inside a visible parent' => [
                'role' => 'editingteacher',
                'parentvisible' => true,
                'delegatedvisible' => true,
                'expected' => true,
            ],
            'Teacher on a hidden subsection inside a visible parent' => [
                'role' => 'editingteacher',
                'parentvisible' => true,
                'delegatedvisible' => false,
                'expected' => true,
            ],
            'Teacher on a visible subsection inside a hidden parent' => [
                'role' => 'editingteacher',
                'parentvisible' => false,
                'delegatedvisible' => true,
                'expected' => true,
            ],
            'Teacher on a hidden subsection inside a hidden parent' => [
                'role' => 'editingteacher',
                'parentvisible' => false,
                'delegatedvisible' => false,
                'expected' => true,
            ],
        ];
    }

    /**
     * Test get_uservisible method when the section is delegated and depending on if the plugin is enabled.
     *
     * @param string $role The role to assign to the user.
     * @param bool $enabled Whether the plugin is enabled.
     * @param bool $expected The expected visibility of the delegated section.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_uservisible_delegate_enabled')]
    public function test_get_uservisible_delegate_enabled(
        string $role,
        bool $enabled,
        bool $expected,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course], ['section' => 1]);

        $modinfo = get_fast_modinfo($course);
        $delegatedsection = $modinfo->get_cm($subsection->cmid)->get_delegated_section_info();

        $user = $this->getDataGenerator()->create_and_enrol($course, $role);

        if (!$enabled) {
            $manager = plugin_manager::resolve_plugininfo_class('mod');
            $manager::enable_plugin('subsection', 0);
            rebuild_course_cache($course->id, true);
        }

        $this->setUser($user);
        $modinfo = get_fast_modinfo($course);

        $delegatedsection = $modinfo->get_section_info($delegatedsection->sectionnum);

        // The get_uservisible is a magic getter.
        $this->assertEquals($expected, $delegatedsection->uservisible);
    }

    /**
     * Data provider for test_get_uservisible_delegate_enabled.
     *
     * @return array
     */
    public static function provider_test_get_uservisible_delegate_enabled(): array {
        return [
            'Student with plugin enabled' => [
                'role' => 'student',
                'enabled' => true,
                'expected' => true,
            ],
            'Student with plugin disabled' => [
                'role' => 'student',
                'enabled' => false,
                'expected' => false,
            ],
            'Teacher with plugin enabled' => [
                'role' => 'editingteacher',
                'enabled' => true,
                'expected' => true,
            ],
            'Teacher with plugin disabled' => [
                'role' => 'editingteacher',
                'enabled' => false,
                'expected' => true,
            ],
        ];
    }

    /**
     * Test get_available method when the section is delegated.
     *
     * @param string $role The role to assign to the user.
     * @param bool $parentavailable The parent section is available.
     * @param bool $delegatedavailable The delegated section is available..
     * @param bool $expectedavailable The expected availability of the delegated section.
     * @param bool $expecteduservisible The expected uservisibility of the delegated section.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_available_delegated')]
    public function test_get_available_delegated(
        string $role,
        bool $parentavailable,
        bool $delegatedavailable,
        bool $expectedavailable,
        bool $expecteduservisible,
    ): void {
        $this->resetAfterTest();

        // The element will be available tomorrow.
        $availability = json_encode(
            (object) [
                'op' => '&',
                'showc' => [true],
                'c' => [
                    [
                        'type' => 'date',
                        'd' => '>=',
                        't' => time() + DAYSECS,
                    ],
                ],
            ]
        );

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        $cmparams = ['section' => 1];
        if (!$delegatedavailable) {
            $cmparams['availability'] = $availability;
        }

        $subsection = $this->getDataGenerator()->create_module(
            'subsection',
            ['course' => $course],
            $cmparams
        );

        $student = $this->getDataGenerator()->create_and_enrol($course, $role);

        $modinfo = get_fast_modinfo($course);

        if (!$parentavailable) {
            formatactions::section($course)->update(
                $modinfo->get_section_info(1),
                ['availability' => $availability],
            );
        }

        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        $delegatedsection = $modinfo->get_cm($subsection->cmid)->get_delegated_section_info();

        // All section_info getters are magic methods.
        $this->assertEquals($expectedavailable, $delegatedsection->available);
        $this->assertEquals($expecteduservisible, $delegatedsection->uservisible);
    }

    /**
     * Data provider for test_get_available_delegated.
     *
     * @return array
     */
    public static function data_provider_get_available_delegated(): array {
        return [
            'Student on an available subsection inside an available parent' => [
                'role' => 'student',
                'parentavailable' => true,
                'delegatedavailable' => true,
                'expectedavailable' => true,
                'expecteduservisible' => true,
            ],
            'Student on an unavailable subsection inside an available parent' => [
                'role' => 'student',
                'parentavailable' => true,
                'delegatedavailable' => false,
                'expectedavailable' => false,
                'expecteduservisible' => false,
            ],
            'Student on an available subsection inside an unavailable parent' => [
                'role' => 'student',
                'parentavailable' => false,
                'delegatedavailable' => true,
                'expectedavailable' => false,
                'expecteduservisible' => false,
            ],
            'Student on an unavailable subsection inside an unavailable parent' => [
                'role' => 'student',
                'parentavailable' => false,
                'delegatedavailable' => false,
                'expectedavailable' => false,
                'expecteduservisible' => false,
            ],
            'Teacher on an available subsection inside an available parent' => [
                'role' => 'editingteacher',
                'parentavailable' => true,
                'delegatedavailable' => true,
                'expectedavailable' => true,
                'expecteduservisible' => true,
            ],
            'Teacher on an unavailable subsection inside an available parent' => [
                'role' => 'editingteacher',
                'parentavailable' => true,
                'delegatedavailable' => false,
                'expectedavailable' => false,
                'expecteduservisible' => true,
            ],
            'Teacher on an available subsection inside an unavailable parent' => [
                'role' => 'editingteacher',
                'parentavailable' => false,
                'delegatedavailable' => true,
                'expectedavailable' => false,
                'expecteduservisible' => true,
            ],
            'Teacher on an unavailable subsection inside an unavailable parent' => [
                'role' => 'editingteacher',
                'parentavailable' => false,
                'delegatedavailable' => false,
                'expectedavailable' => false,
                'expecteduservisible' => true,
            ],
        ];
    }

    /**
     * Test when a section is considered orphan.
     */
    public function test_is_orphan(): void {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course], ['section' => 1]);

        $modinfo = get_fast_modinfo($course);
        $delegatedsection = $modinfo->get_cm($subsection->cmid)->get_delegated_section_info();

        // If mod_subsection is enabled, a subsection is not orphan.
        $modinfo = get_fast_modinfo($course);
        $this->assertFalse($delegatedsection->is_orphan());

        // Delegated sections without a component instance (disabled mod_subsection) is considered orphan.
        $manager = plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('subsection', 0);
        rebuild_course_cache($course->id, true);

        $modinfo = get_fast_modinfo($course);
        $delegatedsection = $modinfo->get_section_info($delegatedsection->sectionnum);
        $this->assertTrue($delegatedsection->is_orphan());

        // Check enabling the plugin restore the previous state.
        $manager::enable_plugin('subsection', 1);
        rebuild_course_cache($course->id, true);

        $modinfo = get_fast_modinfo($course);
        $delegatedsection = $modinfo->get_section_info($delegatedsection->sectionnum);
        $this->assertFalse($delegatedsection->is_orphan());

        // Force section limit in the course format instance.
        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);

        // Core formats does not use numsections anymore. We need to use reflection to change the value.
        $format = course_get_format($course);
        // Add a fake numsections format data (Force loading format data first).
        $format->get_course();
        $reflection = new \ReflectionObject($format);
        $property = $reflection->getProperty('course');
        $courseobject = $property->getValue($format);
        $courseobject->numsections = 1;
        $property->setValue($format, $courseobject);

        $delegatedsection = $modinfo->get_section_info($delegatedsection->sectionnum);
        $this->assertTrue($delegatedsection->is_orphan());
    }

    /**
     * Test for section_info::get_sequence_cm_infos.ma
     */
    public function test_section_get_sequence_cm_infos(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2]);
        $cm1 = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 0]);
        $cm2 = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 1]);
        $cm3 = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 1]);
        $cm4 = $this->getDataGenerator()->create_module('page', ['course' => $course], ['section' => 1]);

        $modinfo = get_fast_modinfo($course->id);

        $sectioninfo = $modinfo->get_section_info(0);
        $cms = $sectioninfo->get_sequence_cm_infos();
        $this->assertCount(1, $cms);
        $this->assertEquals($cm1->cmid, $cms[0]->id);

        $sectioninfo = $modinfo->get_section_info(1);
        $cms = $sectioninfo->get_sequence_cm_infos();
        $this->assertCount(3, $cms);
        $this->assertEquals($cm2->cmid, $cms[0]->id);
        $this->assertEquals($cm3->cmid, $cms[1]->id);
        $this->assertEquals($cm4->cmid, $cms[2]->id);

        $sectioninfo = $modinfo->get_section_info(2);
        $cms = $sectioninfo->get_sequence_cm_infos();
        $this->assertCount(0, $cms);
    }
}
