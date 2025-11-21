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

namespace core_courseformat\output;

use stdClass;

/**
 * Tests for activitybadge class.
 *
 * @package    core_courseformat
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activitybadge::class)]
final class activitybadge_test extends \advanced_testcase {

    /**
     * Test the behaviour of create_instance() and export_for_template() attributes.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_activitybadge_export_for_template(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->setup_scenario();

        $user = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $this->getDataGenerator()->enrol_user(
            $user->id,
            $data->course->id,
            'student'
        );
        $this->setUser($user);

        $renderer = $data->renderer;

        // The activitybadge for a file with all options enabled shouldn't be empty.
        $class = activitybadge::create_instance($data->fileshowtype);
        $result = $class->export_for_template($renderer);
        $this->check_activitybadge($result, 'TXT', 'badge-none');

        // The activitybadge for a file with Show type option disabled should be empty.
        $class = activitybadge::create_instance($data->filehidetype);
        $result = $class->export_for_template($renderer);
        $this->check_activitybadge($result);

        // The activitybadge for a forum with unread messages shouldn't be empty.
        $class = activitybadge::create_instance($data->forumunread);
        $result = $class->export_for_template($renderer);
        $this->check_activitybadge($result, '1 unread post', 'bg-dark text-white');

        // The activitybadge for a forum without unread messages should be empty.
        $class = activitybadge::create_instance($data->forumread);
        $result = $class->export_for_template($renderer);
        $this->check_activitybadge($result);

        // The activitybadge for an assignment should be empty.
        $class = activitybadge::create_instance($data->assign);
        $this->assertNull($class);

        // The activitybadge for a label should be empty.
        $class = activitybadge::create_instance($data->label);
        $this->assertNull($class);
    }

    /**
     * Setup the default scenario, creating some activities:
     * - A forum with one unread message from the teacher.
     * - Another forum without unread messages.
     * - A file with all the appearance options enabled.
     * - A file with the "Show type" option disabled.
     * - An assignment.
     * - A label.
     *
     * @return stdClass the scenario instances.
     */
    private function setup_scenario(): stdClass {
        global $PAGE;

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        // Enrol editing teacher to the course.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user(
            $teacher->id,
            $course->id,
            'editingteacher'
        );
        $this->setUser($teacher);

        // Create a forum with tracking forced and add a discussion.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course->id;
        $record->trackingtype = FORUM_TRACKING_FORCED;
        $forumread = $this->getDataGenerator()->create_module('forum', $record);
        $forumunread = $this->getDataGenerator()->create_module('forum', $record);
        $record = new stdClass();
        $record->course = $course->id;
        $record->userid = $teacher->id;
        $record->forum = $forumunread->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Create a file with all the options enabled.
        $record = (object)[
            'course' => $course->id,
            'showsize' => 1,
            'showtype' => 1,
            'showdate' => 1,
        ];
        $fileshowtype = self::getDataGenerator()->create_module('resource', $record);

        // Create a file with Show type disabled.
        $record = (object)[
            'course' => $course->id,
            'showsize' => 1,
            'showtype' => 0,
            'showdate' => 1,
        ];
        $filehidetype = self::getDataGenerator()->create_module('resource', $record);

        // Create an assignment and a label.
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $label = $this->getDataGenerator()->create_module('label', ['course' => $course->id]);

        rebuild_course_cache($course->id, true);
        $renderer = course_get_format($course->id)->get_renderer($PAGE);
        $modinfo = get_fast_modinfo($course->id);

        return (object)[
            'course' => $course,
            'forumunread' => $modinfo->get_cm($forumunread->cmid),
            'discussion' => $discussion,
            'forumread' => $modinfo->get_cm($forumread->cmid),
            'fileshowtype' => $modinfo->get_cm($fileshowtype->cmid),
            'filehidetype' => $modinfo->get_cm($filehidetype->cmid),
            'assign' => $modinfo->get_cm($assign->cmid),
            'label' => $modinfo->get_cm($label->cmid),
            'renderer' => $renderer,
        ];
    }

    /**
     * Method to check if the result of the export_from_template is the expected.
     *
     * @param stdClass $result The result of the export_from_template() call.
     * @param string|null $content The expected activitybadge content.
     * @param string|null $style The expected activitybadge style.
     * @param string|null $url The expected activitybadge url.
     * @param string|null $elementid The expected activitybadge element id.
     * @param array|null $extra The expected activitybadge extra attributes.
     */
    private function check_activitybadge(
        stdClass $result,
        ?string $content = null,
        ?string $style = null,
        ?string $url = null,
        ?string $elementid = null,
        ?array $extra = null
    ): void {
        if (is_null($content)) {
            $this->assertObjectNotHasProperty('badgecontent', $result);
        } else {
            $this->assertEquals($content, $result->badgecontent);
        }

        if (is_null($style)) {
            $this->assertObjectNotHasProperty('badgestyle', $result);
        } else {
            $this->assertEquals($style, $result->badgestyle);
        }

        if (is_null($url)) {
            $this->assertObjectNotHasProperty('badgeurl', $result);
        } else {
            $this->assertEquals($url, $result->badgeurl);
        }

        if (is_null($elementid)) {
            $this->assertObjectNotHasProperty('badgeelementid', $result);
        } else {
            $this->assertEquals($elementid, $result->badgeelementid);
        }

        if (is_null($extra)) {
            $this->assertObjectNotHasProperty('badgeextraattributes', $result);
        } else {
            $this->assertEquals($extra, $result->badgeextraattributes);
        }
    }
}
