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

/**
 * Test for content webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\content;
use tool_ally\models\component_content;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for content webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_content_test extends abstract_testcase {

    public function test_invalid_component() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->expectExceptionMessage('Invalid component identifier');
        content::service(1, 'aninvalidcomponent', 'anytable', 'anyfield');
    }
    public function test_invalid_table() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $this->expectExceptionMessage('Invalid component identifier');
        content::service($course->id, 'course', 'invalidtable', 'summary');
    }
    public function test_invalid_field() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $this->expectExceptionMessage('Invalid component identifier');
        content::service($course->id, 'course', 'course', 'invalidfield');
    }
    public function test_invalid_id() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $invalidid = 999999;
        $this->expectExceptionMessage('Content not found');
        content::service($invalidid, 'course', 'course', 'summary');
    }

    /**
     * Test the web service when used to get a single course summary content item.
     */
    public function test_service_course_summary() {

        $this->resetAfterTest();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        // Test getting course summary content.
        $coursesummary = '<p>My course summary</p>';
        $course = $this->getDataGenerator()->create_course(['summary' => $coursesummary]);
        $content = content::service($course->id, 'course', 'course', 'summary');
        $expectedurl = (new \moodle_url('/course/edit.php?id='.$course->id))
            ->out(); // Directly converting to string, it should be the same result from the service.
        $expected = new component_content(
            $course->id,
            'course',
            'course',
            'summary',
            null,
            $course->timemodified,
            $course->summaryformat,
            $coursesummary,
            $course->fullname,
            $expectedurl
        );
        $this->assertEquals($expected, $content);
    }

    /**
     * Test the web service when used to get a single course section content item.
     */
    public function test_service_course_section() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Test getting course section summary content.
        $section0summary = '<p>First section summary</p>';
        $section = $this->getDataGenerator()->create_course_section(
            ['section' => 0, 'course' => $course->id]);
        $DB->update_record('course_sections', (object) [
            'id' => $section->id,
            'summary' => $section0summary
        ]);
        $section = $DB->get_record('course_sections', ['id' => $section->id]);
        $content = content::service($section->id, 'course', 'course_sections', 'summary');
        $content->contenturl = null; // We don't want to compare this.
        $expected = new component_content(
            $section->id,
            'course',
            'course_sections',
            'summary',
            null,
            $section->timemodified,
            $section->summaryformat,
            $section0summary,
            'Topic 0' // Default section name for section 0 where no section name set.
        );
        $content->courseid = null;
        $this->assertEquals($expected, $content);
    }

    /**
     * @param string $modname
     * @param string $table
     * @param string $field
     * @return stdClass
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    private function main_module_content_test($modname, $table, $field = 'intro', $titlefield = 'name') {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Test getting mod content.
        $modintro = '<p>My original intro content</p>';
        $mod = $this->getDataGenerator()->create_module($modname,
            ['course' => $course->id, $field => $modintro]);

        $context = \context_module::instance($mod->cmid);
        $filename = 'test image.png';
        $filenameanchor = 'test pdf.pdf';
        $filearea = $field;
        $file = $this->create_test_file($context->id, 'mod_'.$modname, $filearea, 0, $filename);
        $fileanchor = $this->create_test_file($context->id, 'mod_'.$modname, $filearea, 0, $filenameanchor);
        $modinst = $DB->get_record($table, ['id' => $mod->id]);
        $modintro = $modinst->$field.' Modified with image file' .
            '<a href="@@PLUGINFILE@@/'. rawurlencode($filenameanchor).'" alt="test alt anchor" />' . $filenameanchor.'</a>' .
            '<img src="@@PLUGINFILE@@/'. rawurlencode($filename).'" alt="test alt" /></img>';
        $modinst->$field = $modintro;

        $DB->update_record($table, $modinst);

        if ($modname === 'label') {
            $expectedtitle = 'My original intro content'.chr(10).'Modified with image file';
        } else {
            $expectedtitle = $modinst->$titlefield;
        }

        $content = content::service($mod->id, $modname, $table, $field);
        $content->contenturl = null; // We don't want to compare this.
        $expected = new component_content(
            $modinst->id,
            $modname,
            $table,
            $field,
            null,
            $modinst->timemodified,
            $modinst->introformat,
            $modintro,
            $expectedtitle
        );
        $expected->embeddedfiles = [
            [
                'filename' => rawurlencode($fileanchor->get_filename()),
                'contenthash' => $fileanchor->get_contenthash(),
                'pathnamehash' => $fileanchor->get_pathnamehash(),
                'tag' => 'a'
            ],
            [
                'filename' => rawurlencode($file->get_filename()),
                'contenthash' => $file->get_contenthash(),
                'pathnamehash' => $file->get_pathnamehash(),
                'tag' => 'img'
            ]
        ];
        $this->assertEquals($expected, $content);

        return $mod;
    }

    /**
     * Test the web service when used to get a label content item.
     */
    public function test_service_label_content() {
        $this->main_module_content_test('label', 'label');
    }

    /**
     * Test the web service when used to get an assign content item.
     */
    public function test_service_assign_content() {
        $this->main_module_content_test('assign', 'assign');
    }

    /**
     * Test the web service when used to get forum content items.
     */
    public function test_service_forum_content($forumtype = 'forum') {
        $forum = $this->main_module_content_test($forumtype, $forumtype);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a discussion.
        $record = new \stdClass();
        $record->forum = $forum->id;
        $record->userid = $user->id;
        $record->course = $forum->course;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_discussion($record);

        // Add a reply.
        $posttitle = 'My post title';
        $postmessage = 'My post message';
        $record = new \stdClass();
        $record->messageformat = FORMAT_HTML;
        $record->discussion = $discussion->id;
        $record->userid = $user->id;
        $record->subject = $posttitle;
        $record->message = $postmessage;
        $post = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_post($record);

        $this->setAdminUser();

        $content = content::service($post->id, $forumtype, $forumtype.'_posts', 'message');
        $content->contenturl = null; // We don't want to compare this.
        $expected = new component_content(
            $post->id,
            $forumtype,
            $forumtype.'_posts',
            'message',
            null,
            $post->modified,
            $post->messageformat,
            $postmessage,
            $posttitle
        );
        $this->assertEquals($expected, $content);
    }

    public function test_service_hsuforum_content() {
        global $CFG;
        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            $this->test_service_forum_content('hsuforum');
        }
    }

    public function test_service_page_content() {
        $this->main_module_content_test('page', 'page');
        $this->main_module_content_test('page', 'page', 'content');
    }

    public function test_service_lesson_content() {
        global $DB;

        $lesson = $this->main_module_content_test('lesson', 'lesson');
        $this->setAdminUser();
        $lessongenerator = self::getDataGenerator()->get_plugin_generator('mod_lesson');

        $lessonobj = new \lesson($lesson);

        $pagecontents = 'Some text';
        $pagetitle = 'Test title';
        $page = $lessongenerator->create_question_truefalse($lessonobj);
        $page->contents = $pagecontents;
        $page->contentsformat = FORMAT_HTML;
        $page->title = $pagetitle;

        $DB->update_record('lesson_pages', $page);

        $content = content::service($page->id, 'lesson', 'lesson_pages', 'contents');
        $content->contenturl = null; // We don't want to compare this.
        $expected = new component_content(
            $content->id,
            'lesson',
            'lesson_pages',
            'contents',
            null,
            $content->timemodified,
            $page->contentsformat,
            $pagecontents,
            $pagetitle
        );
        $this->assertEquals($expected, $content);
    }

    public function test_service_block_html_content() {
        $this->resetAfterTest();

        $this->setAdminUser();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $context = \context_course::instance($course->id);

        /** @var tool_ally_generator $blockgen */
        $blockgen = $gen->get_plugin_generator('tool_ally');
        $blocktitle = 'Some block';
        $blockcontents = 'Some content';
        $block = $blockgen->add_block($context, $blocktitle, $blockcontents);

        $content = content::service($block->id, 'block_html', 'block_instances', 'configdata');
        $content->contenturl = null; // We don't want to compare this.
        $expected = new component_content(
            $content->id,
            'block_html',
            'block_instances',
            'configdata',
            null,
            $block->timemodified,
            FORMAT_HTML,
            $blockcontents,
            $blocktitle
        );
        $this->assertEquals($expected, $content);
    }

    public function test_service_module_wrong_course() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $field = 'intro';
        $titlefield = 'name';
        $modname = 'page';
        $table = 'page';

        $course = $this->getDataGenerator()->create_course();

        // Test getting mod content.
        $modintro = '<p>My original intro content</p>';
        $mod = $this->getDataGenerator()->create_module($modname,
            ['course' => $course->id, $field => $modintro]);
        $modinst = $DB->get_record($table, ['id' => $mod->id]);
        $expectedmessage = "Content not found for component={$modname}&table={$table}&field={$field}&id={$mod->id}";

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage($expectedmessage);
        content::service($mod->id, $modname, $table, $field, $course->id + 1); // Wrong course.
    }
}
