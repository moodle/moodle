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

namespace mod_label;

/**
 * Unit tests for the activity label's lib.
 *
 * @package    mod_label
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    /**
     * Set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_label_core_calendar_provide_event_action(): void {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_label_core_calendar_provide_event_action_as_non_user(): void {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_label_core_calendar_provide_event_action_in_hidden_section(): void {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id));

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        $sectioninfo = get_fast_modinfo($course->id)->get_section_info(0);
        \core_courseformat\formatactions::section($course->id)->set_visibility($sectioninfo, false);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_label_core_calendar_provide_event_action_for_user(): void {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_label_core_calendar_provide_event_action_already_completed(): void {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('label', $label->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_label_core_calendar_provide_event_action_already_completed_for_user(): void {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('label', $label->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $label->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_label_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Check label name with different content inserted in the label intro.
     *
     * @param string $name
     * @param string $content
     * @param string $format
     * @param string $expectedname
     * @return void
     * @covers       \get_label_name
     * @dataProvider label_get_name_data_provider
     */
    public function test_label_get_label_name(string $name, string $content, string $format, string $expectedname): void {
        $course = $this->getDataGenerator()->create_course();
        // When creating the module, get_label_name is called and fills label->name.
        $label = $this->getDataGenerator()->create_module('label', [
                'name' => $name,
                'course' => $course->id,
                'intro' => $content,
                'introformat' => $format
            ]
        );

        $this->assertEquals($expectedname, $label->name);
    }

    /**
     * Dataprovider for test_label_get_label_name
     *
     * @return array
     */
    public static function label_get_name_data_provider(): array {
        return [
            'withlabelname' => [
                'name' => 'Test label 1',
                'content' => '<p>Simple textual content<p>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Test label 1'
            ],
            'simple' => [
                'name' => '',
                'content' => '<p>Simple textual content<p>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Simple textual content'
            ],
            'empty' => [
                'name' => '',
                'content' => '',
                'format' => FORMAT_HTML,
                'expectedname' => 'Test label 1'
            ],
            'withaudiocontent' => [
                'name' => '',
                'content' => '<p>Test with audio</p>
<p>&nbsp; &nbsp;<audio controls="controls">
<source src="@@PLUGINFILE@@/moodle-hit-song.mp3">
@@PLUGINFILE@@/moodle-hit-song.mp3
</audio>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Test with audio'
            ],
            'withvideo' => [
                'name' => '',
                'content' => '<p>Test video</p>
<p>&nbsp;<video controls="controls">
        <source src="https://www.youtube.com/watch?v=xxxyy">
    https://www.youtube.com/watch?v=xxxyy
</video>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Test video https://www.youtube.com/watch?v=xxxyy'
            ],
            'with video trimming' => [
                'name' => '',
                'content' => '<p>Test with video to be trimmed</p>
<p>&nbsp;<video controls="controls">
        <source src="https://www.youtube.com/watch?v=xxxyy">
    https://www.youtube.com/watch?v=xxxyy
</video>&nbsp;</p>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Test with video to be trimmed https://www.youtube....'
            ],
            'with plain text' => [
                'name' => '',
                'content' => 'Content with @@PLUGINFILE@@/moodle-hit-song.mp3 nothing',
                'format' => FORMAT_HTML,
                'expectedname' => 'Content with nothing'
            ],
            'with several spaces' => [
                'name' => '',
                'content' => "Content with @@PLUGINFILE@@/moodle-hit-song.mp3 \r &nbsp; several spaces",
                'format' => FORMAT_HTML,
                'expectedname' => 'Content with several spaces'
            ],
            'empty spaces' => [
                'name' => '',
                'content' => ' &nbsp; ',
                'format' => FORMAT_HTML,
                'expectedname' => 'Text and media area'
            ],
            'only html' => [
                'name' => '',
                'content' => '<audio controls="controls"><source src=""></audio>',
                'format' => FORMAT_HTML,
                'expectedname' => 'Text and media area'
            ],
            'markdown' => [
                'name' => '',
                'content' => "##Simple Title\n simple markdown format",
                'format' => FORMAT_MARKDOWN,
                'expectedname' => 'Simple Title simple markdown format'
            ],
            'markdown with pluginfile' => [
                'name' => '',
                'content' => "##Simple Title\n simple markdown format @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_MARKDOWN,
                'expectedname' => 'Simple Title simple markdown format'
            ],
            'plain text' => [
                'name' => '',
                'content' => "Simple plain text @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_PLAIN,
                'expectedname' => 'Simple plain text'
            ],
            'moodle format text' => [
                'name' => '',
                'content' => "Simple plain text @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_MOODLE,
                'expectedname' => 'Simple plain text'
            ],
            'html format text' => [
                'name' => '',
                'content' => "<h1>Simple plain title</h1><p> with plain text</p> @@PLUGINFILE@@/moodle-hit-song.mp3",
                'format' => FORMAT_HTML,
                'expectedname' => 'Simple plain title with plain text'
            ],
        ];
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'label';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return \calendar_event::create($event);
    }
}
