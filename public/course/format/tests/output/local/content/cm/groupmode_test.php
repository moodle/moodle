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

namespace core_courseformat\output\local\content\cm;

/**
 * Tests for the group mode class in the course format output.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2026 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(groupmode::class)]
final class groupmode_test extends \advanced_testcase {
    /**
     * Export the group mode of an activity.
     *
     * @param \stdClass $course the course
     * @param int $cmid the course module id
     * @param bool $editing whether edit mode is on
     * @return \stdClass|null the exported data
     */
    private function export_groupmode(\stdClass $course, int $cmid, bool $editing): ?\stdClass {
        global $PAGE, $USER;

        $USER->editing = $editing ? 1 : 0;
        $PAGE = new \moodle_page();
        $PAGE->set_course($course);

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $cm = $modinfo->get_cm($cmid);
        $groupmode = new groupmode($format, $modinfo->get_section_info($cm->sectionnum), $cm);
        return $groupmode->export_for_template($PAGE->get_renderer('core'));
    }

    /**
     * Test the exported data depending on the edit mode and the activity group mode.
     *
     * @param bool $editing whether edit mode is on
     * @param int $groupmode the activity group mode
     * @param bool $expectsdata whether the group mode data is expected
     * @param bool $expectsinteractive whether the interactive dropdown is expected
     * @param string $expectedlabel the expected visible label
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('export_for_template_provider')]
    public function test_export_for_template(
        bool $editing,
        int $groupmode,
        bool $expectsdata,
        bool $expectsinteractive,
        string $expectedlabel,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1, 'groupmode' => $groupmode],
        );
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_groupmode($course, $assign->cmid, $editing);

        if (!$expectsdata) {
            $this->assertNull($data);
            return;
        }
        $this->assertEquals($expectsinteractive, $data->isInteractive);
        $this->assertEquals($expectedlabel, $data->groupalt);
        $this->assertStringContainsString($expectedlabel, $data->accessiblename);
    }

    /**
     * Data provider for test_export_for_template.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {
        return [
            // The control is shown for every value while editing, no groups included.
            'Edit mode, no groups' => [true, NOGROUPS, true, true, 'No groups'],
            'Edit mode, separate groups' => [true, SEPARATEGROUPS, true, true, 'Separate groups'],
            'Edit mode, visible groups' => [true, VISIBLEGROUPS, true, true, 'Visible groups'],
            // Outside edit mode the absence of the indicator already means no groups.
            'View mode, no groups' => [false, NOGROUPS, false, false, ''],
            'View mode, separate groups' => [false, SEPARATEGROUPS, true, false, 'Separate groups'],
            'View mode, visible groups' => [false, VISIBLEGROUPS, true, false, 'Visible groups'],
        ];
    }

    /**
     * Test the exported data when the course forces the group mode.
     *
     * @param bool $editing whether edit mode is on
     * @param int $coursegroupmode the group mode forced by the course
     * @param bool $expectsdata whether the group mode data is expected
     * @param string $expectedlabel the expected visible label
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('forced_groupmode_provider')]
    public function test_forced_groupmode(
        bool $editing,
        int $coursegroupmode,
        bool $expectsdata,
        string $expectedlabel,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'numsections' => 1,
            'groupmode' => $coursegroupmode,
            'groupmodeforce' => 1,
        ]);

        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1, 'groupmode' => VISIBLEGROUPS],
        );
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_groupmode($course, $assign->cmid, $editing);

        if (!$expectsdata) {
            $this->assertNull($data);
            return;
        }

        $this->assertFalse($data->isInteractive);
        $this->assertEquals($expectedlabel, $data->groupalt);
    }

    /**
     * Data provider for test_forced_groupmode.
     *
     * @return array
     */
    public static function forced_groupmode_provider(): array {
        return [
            // No groups is only indicated while editing, so the teacher knows why there is no control.
            'Edit mode, forced no groups' => [true, NOGROUPS, true, 'No groups'],
            'View mode, forced no groups' => [false, NOGROUPS, false, ''],
            'Edit mode, forced separate groups' => [true, SEPARATEGROUPS, true, 'Separate groups'],
            'View mode, forced separate groups' => [false, SEPARATEGROUPS, true, 'Separate groups'],
        ];
    }

    /**
     * Test nothing is exported for an activity that does not support groups.
     */
    public function test_module_without_group_support(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'section' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $this->assertNull($this->export_groupmode($course, $page->cmid, true));
        $this->assertNull($this->export_groupmode($course, $page->cmid, false));
    }

    /**
     * Test nothing is exported for a user who cannot manage activities.
     */
    public function test_without_manage_activities_capability(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1, 'groupmode' => SEPARATEGROUPS],
        );
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $this->assertNull($this->export_groupmode($course, $assign->cmid, false));
    }

    /**
     * Test the React props of the interactive dropdown.
     */
    public function test_react_props(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1, 'groupmode' => SEPARATEGROUPS],
        );
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_groupmode($course, $assign->cmid, true);
        $props = json_decode($data->reactprops);

        $this->assertEquals($assign->cmid, $props->cmid);
        $this->assertEquals(SEPARATEGROUPS, $props->value);
        $this->assertEquals(
            [NOGROUPS, SEPARATEGROUPS, VISIBLEGROUPS],
            array_column($props->options, 'value'),
        );
        $this->assertEquals(
            ['cmNoGroups', 'cmSeparateGroups', 'cmVisibleGroups'],
            array_column($props->options, 'mutation'),
        );
        $this->assertEquals(
            ['No groups', 'Separate groups', 'Visible groups'],
            array_column($props->options, 'label'),
        );
        // The control shows a shorter label than the menu.
        $this->assertEquals(
            ['No groups', 'Separate', 'Visible'],
            array_column($props->options, 'triggerlabel'),
        );
    }

    /**
     * Test the accessible name of every value contains the label shown on the control.
     */
    public function test_accessible_name_contains_label(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1, 'groupmode' => SEPARATEGROUPS],
        );
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_groupmode($course, $assign->cmid, true);
        $props = json_decode($data->reactprops);

        foreach ($props->options as $option) {
            $this->assertStringContainsString($option->triggerlabel, $option->accessiblename);
        }
    }
}
