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
 * Tests for the visibility class in the course format output.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(visibility::class)]
final class visibility_test extends \advanced_testcase {
    /**
     * Export the visibility of an activity.
     *
     * @param \stdClass $course the course
     * @param int $cmid the course module id
     * @param bool $editing whether edit mode is on
     * @return \stdClass|null the exported data
     */
    private function export_visibility(\stdClass $course, int $cmid, bool $editing): ?\stdClass {
        global $PAGE, $USER;

        $USER->editing = $editing ? 1 : 0;
        // Edit mode depends on the page context, so the page must be a course page.
        $PAGE = new \moodle_page();
        $PAGE->set_course($course);

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $cm = $modinfo->get_cm($cmid);
        $visibility = new visibility($format, $modinfo->get_section_info($cm->sectionnum), $cm);
        return $visibility->export_for_template($PAGE->get_renderer('core'));
    }

    /**
     * Test the exported data depending on the edit mode and the activity visibility.
     *
     * @param bool $editing whether edit mode is on
     * @param array $modoptions the activity options
     * @param bool $expectsdata whether the visibility data is expected
     * @param bool $expectsinteractive whether the interactive dropdown is expected
     * @param string $expectedstate the expected visibility state
     * @param string $expectedlabel the expected visible label
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('export_for_template_provider')]
    public function test_export_for_template(
        bool $editing,
        array $modoptions,
        bool $expectsdata,
        bool $expectsinteractive,
        string $expectedstate,
        string $expectedlabel,
    ): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 1;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1] + $modoptions);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_visibility($course, $assign->cmid, $editing);

        if (!$expectsdata) {
            $this->assertNull($data);
            return;
        }
        $this->assertEquals($expectsinteractive, $data->isInteractive);
        $this->assertEquals($expectedstate, $data->state);
        $this->assertEquals($expectedlabel, $data->label);
    }

    /**
     * Data provider for test_export_for_template.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {
        return [
            'Edit mode, shown' => [true, [], true, true, 'show', 'Available'],
            'Edit mode, hidden' => [true, ['visible' => 0], true, true, 'hide', 'Hidden'],
            'Edit mode, stealth' => [true, ['visibleoncoursepage' => 0], true, true, 'stealth', 'Not shown'],
            'View mode, shown' => [false, [], false, false, '', ''],
            'View mode, hidden' => [false, ['visible' => 0], true, false, 'hide', ''],
            'View mode, stealth' => [false, ['visibleoncoursepage' => 0], true, false, 'stealth', ''],
        ];
    }

    /**
     * Test the React props of the interactive dropdown.
     */
    public function test_react_props(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 1;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1, 'visible' => 0]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_visibility($course, $assign->cmid, true);
        $props = json_decode($data->reactprops);

        $this->assertEquals($assign->cmid, $props->cmid);
        $this->assertEquals('hide', $props->value);
        $this->assertEquals(['show', 'hide', 'stealth'], array_column($props->options, 'value'));
        $this->assertEquals(['cmShow', 'cmHide', 'cmStealth'], array_column($props->options, 'mutation'));
        // Each option carries what the trigger shows once it is applied.
        $this->assertEquals(['show', 'hide', 'stealth'], array_column($props->options, 'state'));
        $this->assertEquals(['Available', 'Hidden', 'Not shown'], array_column($props->options, 'triggerlabel'));
        foreach ($props->options as $option) {
            $this->assertStringContainsStringIgnoringCase($option->triggerlabel, $option->accessiblename);
        }
    }

    /**
     * Test showing an activity in a hidden section is announced as stealth.
     */
    public function test_react_props_in_hidden_section(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 1;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1, 'visible' => 0]);
        $sectioninfo = get_fast_modinfo($course)->get_section_info(1);
        \core_courseformat\formatactions::section($course->id)->set_visibility($sectioninfo, false);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $data = $this->export_visibility($course, $assign->cmid, true);
        $props = json_decode($data->reactprops);
        $show = array_values(array_filter($props->options, fn($option) => $option->value === 'show'));

        $this->assertNotEmpty($show);
        $this->assertEquals('stealth', $show[0]->state);
        $this->assertEquals('Not shown', $show[0]->triggerlabel);
    }

    /**
     * Test the accessible name of every state contains its visible label.
     */
    public function test_accessible_name_contains_label(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 1;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        foreach ([[], ['visible' => 0], ['visibleoncoursepage' => 0]] as $modoptions) {
            $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1] + $modoptions);
            $data = $this->export_visibility($course, $assign->cmid, true);
            $this->assertStringContainsStringIgnoringCase($data->label, $data->accessiblename);
        }
    }

    /**
     * Test the stealth option is only available when stealth activities are allowed.
     */
    public function test_options_without_stealth(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 0;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $props = json_decode($this->export_visibility($course, $assign->cmid, true)->reactprops);

        $this->assertEquals(['show', 'hide'], array_column($props->options, 'value'));
    }

    /**
     * Test a shown activity inside a hidden section.
     */
    public function test_shown_activity_in_hidden_section(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->allowstealth = 1;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1]);
        // Hiding a section hides its activities, so the activity is shown again afterwards.
        $sectioninfo = get_fast_modinfo($course)->get_section_info(1);
        \core_courseformat\formatactions::section($course)->set_visibility($sectioninfo, false);
        \core_courseformat\formatactions::cm($course)->set_visibility($assign->cmid, 1);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $props = json_decode($this->export_visibility($course, $assign->cmid, true)->reactprops);
        $this->assertEquals(['show', 'hide'], array_column($props->options, 'value'));

        $data = $this->export_visibility($course, $assign->cmid, false);
        $this->assertFalse($data->isInteractive);
        $this->assertEquals('stealth', $data->state);
    }

    /**
     * Test a teacher without the visibility capability gets a static indicator for every state while editing,
     * and only for non-shown activities outside edit mode.
     */
    public function test_without_visibility_capability(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $shown = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1]);
        $hidden = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'section' => 1, 'visible' => 0]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('moodle/course:activityvisibility', CAP_PROHIBIT, $roleid, \context_course::instance($course->id));
        $this->getDataGenerator()->role_assign($roleid, $teacher->id, \context_course::instance($course->id));
        $this->setUser($teacher);

        $data = $this->export_visibility($course, $shown->cmid, true);
        $this->assertFalse($data->isInteractive);
        $this->assertEquals('show', $data->state);
        $this->assertEquals('Available', $data->label);
        $this->assertEquals('Availability: Available on course page', $data->accessiblename);
        $this->assertNull($this->export_visibility($course, $shown->cmid, false));

        $data = $this->export_visibility($course, $hidden->cmid, false);
        $this->assertFalse($data->isInteractive);
        $this->assertEquals('hide', $data->state);
        $this->assertEquals('', $data->label);

        $data = $this->export_visibility($course, $hidden->cmid, true);
        $this->assertFalse($data->isInteractive);
        $this->assertEquals('hide', $data->state);
        $this->assertEquals('Hidden', $data->label);
        $this->assertEquals('Availability: Hidden from students', $data->accessiblename);
        $this->assertEquals('Hidden from students', $data->tooltip);
    }
}
