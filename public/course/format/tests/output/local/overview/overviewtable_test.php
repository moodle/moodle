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

namespace core_courseformat\output\local\overview;

/**
 * Tests for courseformat
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewtable::class)]
final class overviewtable_test extends \advanced_testcase {
    /**
     * Test export_for_external method.
     *
     * @param string $role The role of the user.
     * @param int $expectedactivities The expected number of activities visible to the user.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_export_for_external')]
    public function test_export_for_external(
        string $role,
        int $expectedactivities,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Ensure the course is fully loaded.
        $course = get_course($course->id);

        // Create three pages, one of them unavailable and hidden to students.
        $mods = [
            'page1' => $this->getDataGenerator()->create_module('page', ['course' => $course->id]),
            'page2' => $this->getDataGenerator()->create_module('page', ['course' => $course->id]),
        ];
        $availabilityjson = json_encode(\core_availability\tree::get_root_json(
            [
                \availability_date\condition::get_json(
                    \availability_date\condition::DIRECTION_FROM,
                    time() + 3600,
                ),
            ],
            '&',
            false,
        ));
        $mods['page3'] = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id,
            'visible' => true,
            'availability' => $availabilityjson,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        $this->setUser($user);

        $modinfo = get_fast_modinfo($course);

        $cms = [
            'page1' => $modinfo->get_cm($mods['page1']->cmid),
            'page2' => $modinfo->get_cm($mods['page2']->cmid),
            'page3' => $modinfo->get_cm($mods['page3']->cmid),
        ];

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $overviewtable = new overviewtable($course, 'page');

        $data = $overviewtable->export_for_external();

        $templatedata = $overviewtable->export_for_template($renderer);

        $this->assertEquals($course, $data->course);
        $this->assertEquals(false, $data->hasintegration);

        foreach ($templatedata->headers as $header) {
            $this->assertObjectHasProperty('name', $header);
            $this->assertObjectHasProperty('key', $header);
            $this->assertObjectHasProperty('textalign', $header);
            $this->assertObjectHasProperty('align', $header);
            $this->assertCount(4, get_object_vars($header));
        }

        $this->assertCount(4, get_object_vars($data));

        $this->assertCount($expectedactivities, $data->activities);

        $this->assertEquals($cms['page1'], $data->activities[0]->cm);
        $this->assertEquals(false, $data->activities[0]->haserror);
        $this->assertCount(count($data->headers), $data->activities[0]->items);

        foreach ($data->headers as $index => $header) {
            foreach ($data->activities as $activity) {
                $this->assertCount(count($data->headers), $activity->items);
                $this->assertEquals($header->name, $activity->items[$index]->get_name());
                $this->assertEquals($header->key, $activity->items[$index]->get_key());
            }
        }
    }

    /**
     * Data provider for test_export_for_external.
     *
     * @return \Generator The data provider array.
     */
    public static function provider_export_for_external(): \Generator {
        yield 'Editing teacher' => [
            'role' => 'editingteacher',
            'expectedactivities' => 3,
        ];
        yield 'Non-editing teacher' => [
            'role' => 'teacher',
            'expectedactivities' => 3,
        ];
        yield 'Student' => [
            'role' => 'student',
            'expectedactivities' => 2,
        ];
    }

    /**
     * Test is_cm_displayable method for various visibility and stealth combinations.
     *
     * @param string $role The role of the user.
     * @param bool $visible Whether the activity is visible.
     * @param bool $stealth Whether the activity is displayed in the course page or not.
     * @param bool $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_is_cm_displayable_visibility')]
    public function test_is_cm_displayable_visibility(
        string $role,
        bool $visible,
        bool $stealth,
        bool $expected
    ): void {
        $this->resetAfterTest();
        set_config('allowstealth', true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        $this->setUser($user);

        $mod = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id,
            'visible' => $visible,
            'visibleoncoursepage' => !$stealth,
        ]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $this->assertEquals($visible, $cm->visible);
        $this->assertEquals($stealth, !$cm->visibleoncoursepage);

        $this->assertEquals($expected, overviewtable::is_cm_displayable($cm));
    }

    /**
     * Data provider for test_is_cm_displayable_visibility.
     *
     * @return \Generator The data provider array.
     */
    public static function provider_is_cm_displayable_visibility(): \Generator {
        yield 'Editing teacher - Visible' => [
            'role' => 'editingteacher',
            'visible' => true,
            'stealth' => false,
            'expected' => true,
        ];
        yield 'Editing teacher - Hidden' => [
            'role' => 'editingteacher',
            'visible' => false,
            'stealth' => false,
            'expected' => true,
        ];
        yield 'Editing teacher - Stealth' => [
            'role' => 'editingteacher',
            'visible' => true,
            'stealth' => true,
            'expected' => true,
        ];
        yield 'Teacher - Visible' => [
            'role' => 'teacher',
            'visible' => true,
            'stealth' => false,
            'expected' => true,
        ];
        yield 'Teacher - Hidden' => [
            'role' => 'teacher',
            'visible' => false,
            'stealth' => false,
            'expected' => true,
        ];
        yield 'Teacher - Stealth' => [
            'role' => 'teacher',
            'visible' => true,
            'stealth' => true,
            'expected' => true,
        ];
        yield 'Student - Visible' => [
            'role' => 'student',
            'visible' => true,
            'stealth' => false,
            'expected' => true,
        ];
        yield 'Student - Hidden' => [
            'role' => 'student',
            'visible' => false,
            'stealth' => false,
            'expected' => false,
        ];
        yield 'Student - Stealth' => [
            'role' => 'student',
            'visible' => true,
            'stealth' => true,
            'expected' => true,
        ];
    }

    /**
     * Test is_cm_displayable method for modules without view page (like folder or text&media).
     */
    public function test_is_cm_displayable_without_view_page(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);

        $modfolder = $this->getDataGenerator()->create_module('folder', [
            'course' => $course->id,
            'visible' => 1,
        ]);
        $modtext = $this->getDataGenerator()->create_module('label', [
            'course' => $course->id,
            'visible' => 1,
        ]);
        $modinfo = get_fast_modinfo($course);
        $cmfolder = $modinfo->get_cm($modfolder->cmid);
        $cmtext = $modinfo->get_cm($modtext->cmid);

        $this->assertTrue(overviewtable::is_cm_displayable($cmfolder));
        $this->assertFalse(overviewtable::is_cm_displayable($cmtext));
    }

    /**
     * Test is_cm_displayable method for various availability combinations.
     *
     * @param string $role The role of the user.
     * @param bool $visible Whether the activity is visible.
     * @param bool $isavailable Whether the activity is available.
     * @param bool $availabilityvisible Whether the availability info is shown.
     * @param bool $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_is_cm_displayable_availability')]
    public function test_is_cm_displayable_availability(
        string $role,
        bool $visible,
        bool $isavailable,
        bool $availabilityvisible,
        bool $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        $this->setUser($user);

        // Set up the availability settings.
        $operation = \availability_date\condition::DIRECTION_FROM;
        $time = $isavailable ? time() - 3600 : time() + 3600;
        $availabilityjson = json_encode(\core_availability\tree::get_root_json(
            [
                \availability_date\condition::get_json($operation, $time),
            ],
            '&',
            $availabilityvisible,
        ));

        $mod = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id,
            'visible' => $visible,
            'availability' => $availabilityjson,
        ]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $this->assertEquals($visible, $cm->visible);
        $this->assertEquals($isavailable, $cm->available);
        if ($isavailable || !$visible) {
            $this->assertEmpty($cm->availableinfo);
        } else {
            $this->assertEquals($availabilityvisible, !empty($cm->availableinfo));
        }

        $this->assertEquals($expected, overviewtable::is_cm_displayable($cm));
    }

    /**
     * Data provider for test_is_cm_displayable_availability.
     *
     * @return \Generator The data provider array.
     */
    public static function provider_is_cm_displayable_availability(): \Generator {
        yield 'Teacher - Visible - Available visible' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Visible - Unavailable visible' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Visible - Unavailable hidden' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Available visible' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Unavailable visible' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Unavailable hidden' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => true,
        ];
        yield 'Student - Visible - Available visible' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Student - Visible - Unavailable visible' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Student - Visible - Unavailable hidden' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => false,
        ];
        yield 'Student - Hidden - Available visible' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => false,
        ];
        yield 'Student - Hidden - Unavailable visible' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => false,
        ];
        yield 'Student - Hidden - Unavailable hidden' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => false,
        ];
    }

    /**
     * Test is_cm_available method.
     *
     * @param string $role The role of the user.
     * @param bool $visible Whether the activity is visible.
     * @param bool $isavailable Whether the activity is available.
     * @param bool $availabilityvisible Whether the availability info is shown.
     * @param bool $expected The expected result.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_is_cm_available')]
    public function test_is_cm_available(
        string $role,
        bool $visible,
        bool $isavailable,
        bool $availabilityvisible,
        bool $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        $this->setUser($user);

        // Set up the availability settings.
        $operation = \availability_date\condition::DIRECTION_FROM;
        $time = $isavailable ? time() - 3600 : time() + 3600;
        $availabilityjson = json_encode(\core_availability\tree::get_root_json(
            [
                \availability_date\condition::get_json($operation, $time),
            ],
            '&',
            $availabilityvisible,
        ));

        $mod = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id,
            'visible' => $visible,
            'availability' => $availabilityjson,
        ]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $this->assertEquals($visible, $cm->visible);
        $this->assertEquals($isavailable, $cm->available);
        if ($isavailable || !$visible) {
            $this->assertEmpty($cm->availableinfo);
        } else {
            $this->assertEquals($availabilityvisible, !empty($cm->availableinfo));
        }

        $this->assertEquals($expected, overviewtable::is_cm_available($cm));
    }

    /**
     * Data provider for test_is_cm_available.
     *
     * @return \Generator The data provider array.
     */
    public static function provider_is_cm_available(): \Generator {
        yield 'Teacher - Visible - Available visible' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Visible - Unavailable visible' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Visible - Unavailable hidden' => [
            'role' => 'teacher',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Available visible' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Unavailable visible' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Teacher - Hidden - Unavailable hidden' => [
            'role' => 'teacher',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => true,
        ];
        yield 'Student - Visible - Available visible' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Student - Visible - Unavailable visible' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => false,
        ];
        yield 'Student - Visible - Unavailable hidden' => [
            'role' => 'student',
            'visible' => true,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => false,
        ];
        yield 'Student - Hidden - Available visible' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => true,
            'availabilityvisible' => true,
            'expected' => true,
        ];
        yield 'Student - Hidden - Unavailable visible' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => true,
            'expected' => false,
        ];
        yield 'Student - Hidden - Unavailable hidden' => [
            'role' => 'student',
            'visible' => false,
            'isavailable' => false,
            'availabilityvisible' => false,
            'expected' => false,
        ];
    }
}
