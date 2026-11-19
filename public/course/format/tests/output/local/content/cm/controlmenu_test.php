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
 * Tests for the activity control menu in the course format output.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2026 Aya Saad <aya.saad@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(controlmenu::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(visibility::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(groupmode::class)]
final class controlmenu_test extends \advanced_testcase {
    /**
     * Get the control menu items of an activity.
     *
     * @param \stdClass $course the course
     * @param int $cmid the course module id
     * @param bool $editing whether edit mode is on
     * @param \core_courseformat\base|null $format the course format, or null to use the course one
     * @return array the menu items, keyed by control name
     */
    private function get_control_items(
        \stdClass $course,
        int $cmid,
        bool $editing,
        ?\core_courseformat\base $format = null,
    ): array {
        global $PAGE, $USER;

        $USER->editing = $editing ? 1 : 0;
        $PAGE = new \moodle_page();
        $PAGE->set_course($course);

        $format = $format ?? course_get_format($course);
        $modinfo = $format->get_modinfo();
        $cm = $modinfo->get_cm($cmid);
        $controlmenu = new controlmenu($format, $modinfo->get_section_info($cm->sectionnum), $cm);
        return $controlmenu->get_cm_control_items();
    }

    /**
     * Test the availability and group mode items are only offered when the card cannot change them.
     *
     * @param bool $editing whether edit mode is on
     * @param bool $groupmodeforce whether the course forces the group mode
     * @param bool $expectsavailability whether the availability item is expected
     * @param bool $expectsgroupmode whether the group mode item is expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('card_controls_provider')]
    public function test_card_controls_are_not_repeated(
        bool $editing,
        bool $groupmodeforce,
        bool $expectsavailability,
        bool $expectsgroupmode,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'numsections' => 1,
            'groupmode' => SEPARATEGROUPS,
            'groupmodeforce' => $groupmodeforce ? 1 : 0,
        ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id, 'section' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $items = $this->get_control_items($course, $forum->cmid, $editing);

        $this->assertSame($expectsavailability, !empty($items['availability']));
        $this->assertSame($expectsgroupmode, !empty($items['groupmode']));
        // The rest of the menu is not affected.
        $this->assertNotEmpty($items['update']);
        $this->assertNotEmpty($items['delete']);
    }

    /**
     * Data provider for test_card_controls_are_not_repeated.
     *
     * @return array
     */
    public static function card_controls_provider(): array {
        return [
            'Editing, the card shows both controls' => [
                'editing' => true,
                'groupmodeforce' => false,
                'expectsavailability' => false,
                'expectsgroupmode' => false,
            ],
            'Editing, forced group mode only shows an indicator on the card' => [
                'editing' => true,
                'groupmodeforce' => true,
                'expectsavailability' => false,
                'expectsgroupmode' => false,
            ],
            'Not editing, the card shows no controls' => [
                'editing' => false,
                'groupmodeforce' => false,
                'expectsavailability' => true,
                'expectsgroupmode' => true,
            ],
        ];
    }

    /**
     * Test the menu keeps the availability and group mode items when the card cannot change them while editing.
     */
    public function test_card_controls_kept_without_components(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1, 'groupmode' => SEPARATEGROUPS]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id, 'section' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        // Load the format library before extending its class.
        course_get_format($course);
        // A format without components only shows read-only indicators on the card.
        $format = new class ($course->id) extends \format_topics {
            /**
             * Create a Topics format for the course.
             *
             * @param int $courseid the course id
             */
            public function __construct(int $courseid) {
                parent::__construct('topics', $courseid);
            }

            /**
             * Disable the components support.
             *
             * @return bool
             */
            public function supports_components() {
                return false;
            }
        };

        $items = $this->get_control_items($course, $forum->cmid, true, $format);

        $this->assertNotEmpty($items['availability']);
        $this->assertNotEmpty($items['groupmode']);
    }
}
