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

use PHPUnit\Framework\Attributes\CoversFunction;

/**
 * Unit tests for the activity label's lib.
 *
 * @package    mod_label
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversFunction('label_generate_image_from_details')]
#[CoversFunction('label_dndupload_handle')]
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
     * A custom size within the admin resize limits is honoured as-is on the img tag.
     */
    public function test_label_generate_image_from_details_with_alt_and_size(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        // Well below the default 400x400 resize cap.
        set_config('dndresizewidth', 400, 'label');
        set_config('dndresizeheight', 400, 'label');
        $file = $this->create_draft_image_file($USER->id, 'photo.png', null, 600, 450);

        $html = label_generate_image_from_details($file, [
            'alt' => 'A photo of a beach',
            'presentation' => false,
            'width' => 320,
            'height' => 240,
        ]);

        $this->assertStringContainsString('alt="A photo of a beach"', $html);
        $this->assertStringContainsString('width="320"', $html);
        $this->assertStringContainsString('height="240"', $html);
        $this->assertStringContainsString('class="img-fluid"', $html);
        $this->assertStringNotContainsString('role="presentation"', $html);
    }

    /**
     * A decorative image gets an empty alt text and the presentation role.
     */
    public function test_label_generate_image_from_details_decorative(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        $file = $this->create_draft_image_file($USER->id, 'photo.png');

        $html = label_generate_image_from_details($file, [
            'alt' => 'This should be ignored for a decorative image',
            'presentation' => true,
            'width' => 0,
            'height' => 0,
        ]);

        $this->assertStringContainsString('alt=""', $html);
        $this->assertStringContainsString('role="presentation"', $html);
        $this->assertStringNotContainsString('This should be ignored', $html);
    }

    /**
     * A decorative image that is resized still gets a smaller physical file, but is not wrapped in a link
     * to the original: its empty alt text would leave that link with no accessible name.
     */
    public function test_label_generate_image_from_details_decorative_resized_not_linked(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        set_config('dndresizewidth', 400, 'label');
        set_config('dndresizeheight', 400, 'label');
        $file = $this->create_draft_image_file($USER->id, 'big.png', null, 800, 600);

        $html = label_generate_image_from_details($file, [
            'alt' => 'This should be ignored for a decorative image',
            'presentation' => true,
            'width' => 0,
            'height' => 0,
        ]);

        $this->assertStringContainsString('role="presentation"', $html);
        // A smaller physical file is still generated so the full-resolution original is not embedded.
        $this->assertStringContainsString('s_big.png', $html);
        // But it is not wrapped in a link: an empty-alt image link has no accessible name.
        $this->assertStringNotContainsString('<a href=', $html);
    }

    /**
     * A custom size larger than the admin resize limits is capped, keeping the aspect ratio, and a
     * smaller physical file is generated and linked to the original.
     */
    public function test_label_generate_image_from_details_caps_oversized_custom_size(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        set_config('dndresizewidth', 400, 'label');
        set_config('dndresizeheight', 400, 'label');
        $file = $this->create_draft_image_file($USER->id, 'big.png', null, 800, 600);

        $html = label_generate_image_from_details($file, [
            'alt' => 'A large photo',
            'presentation' => false,
            'width' => 800,
            'height' => 600,
        ]);

        // 800x600 capped to the 400 width limit, keeping proportions.
        $this->assertStringContainsString('width="400"', $html);
        $this->assertStringContainsString('height="300"', $html);
        // A smaller physical file is generated and the original is linked.
        $this->assertStringContainsString('s_big.png', $html);
        $this->assertStringContainsString('<a href=', $html);
    }

    /**
     * The "Original" size (no explicit width/height) still caps the embedded file at the admin limits
     * and leaves the display size implicit on the img tag.
     */
    public function test_label_generate_image_from_details_original_caps_embedded_file(): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        set_config('dndresizewidth', 400, 'label');
        set_config('dndresizeheight', 400, 'label');
        $file = $this->create_draft_image_file($USER->id, 'orig.png', null, 800, 600);

        $html = label_generate_image_from_details($file, [
            'alt' => 'A large photo shown at its original size',
            'presentation' => false,
            'width' => 0,
            'height' => 0,
        ]);

        // Original mode does not force explicit dimensions on the tag.
        $this->assertStringNotContainsString('width=', $html);
        $this->assertStringNotContainsString('height=', $html);
        // But the embedded file is still capped: a smaller file is generated and the original linked.
        $this->assertStringContainsString('s_orig.png', $html);
        $this->assertStringContainsString('<a href=', $html);
    }

    /**
     * When the dnd upload carries image details, the created label uses them for the embedded image.
     */
    public function test_label_dndupload_handle_uses_image_details(): void {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');
        require_once($CFG->dirroot . '/course/modlib.php');

        $course = $this->getDataGenerator()->create_course();

        // Create an empty label course module, exactly as the dnd ajax processor does before handing over.
        [$module, $context, $cw, $cm, $data] = prepare_new_moduleinfo_data($course, 'label', 0);
        $data->coursemodule = $data->id = add_course_module($data);

        set_config('dndresizewidth', 400, 'label');
        set_config('dndresizeheight', 400, 'label');
        $draftitemid = file_get_unused_draft_itemid();
        $this->create_draft_image_file($USER->id, 'beach.png', $draftitemid, 600, 450);

        $uploadinfo = (object) [
            'type' => 'Files',
            'course' => $course,
            'coursemodule' => $data->coursemodule,
            'displayname' => 'beach',
            'draftitemid' => $draftitemid,
            'imagedetails' => [
                'alt' => 'A quiet beach at sunset',
                'presentation' => false,
                'width' => 300,
                'height' => 200,
            ],
        ];

        $instanceid = label_dndupload_handle($uploadinfo);

        $label = $DB->get_record('label', ['id' => $instanceid], '*', MUST_EXIST);
        $this->assertStringContainsString('alt="A quiet beach at sunset"', $label->intro);
        $this->assertStringContainsString('width="300"', $label->intro);
        $this->assertStringContainsString('height="200"', $label->intro);
    }

    /**
     * Without image details, the dnd upload keeps its existing resized-image behaviour.
     */
    public function test_label_dndupload_handle_without_image_details(): void {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/label/lib.php');
        require_once($CFG->dirroot . '/course/modlib.php');

        $course = $this->getDataGenerator()->create_course();

        [$module, $context, $cw, $cm, $data] = prepare_new_moduleinfo_data($course, 'label', 0);
        $data->coursemodule = $data->id = add_course_module($data);

        $draftitemid = file_get_unused_draft_itemid();
        $this->create_draft_image_file($USER->id, 'beach.png', $draftitemid);

        $uploadinfo = (object) [
            'type' => 'Files',
            'course' => $course,
            'coursemodule' => $data->coursemodule,
            'displayname' => 'beach',
            'draftitemid' => $draftitemid,
        ];

        $instanceid = label_dndupload_handle($uploadinfo);

        $label = $DB->get_record('label', ['id' => $instanceid], '*', MUST_EXIST);
        // The existing behaviour derives the alt text from the file name.
        $this->assertStringContainsString('alt="beach.png"', $label->intro);
    }

    /**
     * Create an image file of a given size in a user's draft file area for dnd upload tests.
     *
     * @param int $userid the owner of the draft area
     * @param string $filename the file name to give the image
     * @param int|null $draftitemid the draft item id to use (a new one is generated when null)
     * @param int $width the pixel width of the generated PNG
     * @param int $height the pixel height of the generated PNG
     * @return \stored_file the created draft file
     */
    private function create_draft_image_file(
        int $userid,
        string $filename,
        ?int $draftitemid = null,
        int $width = 1,
        int $height = 1,
    ): \stored_file {
        // Generate a real PNG of the requested size so get_imageinfo() and thumbnail generation work.
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 150, 100));
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        $fs = get_file_storage();
        $record = [
            'contextid' => \context_user::instance($userid)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid ?? file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => $filename,
        ];
        return $fs->create_file_from_string($record, $png);
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
