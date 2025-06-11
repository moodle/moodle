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
 * Testcase class for the tool_ally\components\hsuforum_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\componentsupport\hsuforum_component;
use tool_ally\local_content;
use tool_ally\componentsupport\forum_component;
use tool_ally\testing\traits\component_assertions;

defined('MOODLE_INTERNAL') || die();

require_once('components_forum_component_test.php');

/**
 * Testcase class for the tool_ally\components\hsuforum_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_hsuforum_component_test extends abstract_testcase {

    use component_assertions;

    /**
     * @var string
     */
    private $forumtype = 'hsuforum';

    /**
     * @var stdClass
     */
    private $student;

    /**
     * @var stdClass
     */
    private $teacher;

    /**
     * @var stdClass
     */
    private $admin;

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var context_course
     */
    private $coursecontext;

    /**
     * @var stdClass
     */
    private $forum;

    /**
     * @var stdClass
     */
    private $studentdiscussion;

    /**
     * @var stdClass
     */
    private $teacherdiscussion;

    /**
     * @var forum_component
     */
    private $component;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->student = $gen->create_user();
        $this->teacher = $gen->create_user();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $gen->enrol_user($this->student->id, $this->course->id, 'student');
        $gen->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');
        $forumdata = [
            'course' => $this->course->id,
            'introformat' => FORMAT_HTML,
            'intro' => '<p>My intro for forum type '.$this->forumtype.'</p>'
        ];
        $this->forum = $gen->create_module($this->forumtype, $forumdata);

        // Add a discussion / post by teacher - should show up in results.
        $this->setUser($this->teacher);
        $record = new \stdClass();
        $record->course = $this->course->id;
        $record->forum = $this->forum->id;
        $record->userid = $this->teacher->id;
        $this->teacherdiscussion = self::getDataGenerator()->get_plugin_generator(
            'mod_'.$this->forumtype)->create_discussion($record);

        // Add a discussion / post by student - should NOT show up in results.
        $this->setUser($this->student);
        $record = new \stdClass();
        $record->course = $this->course->id;
        $record->forum = $this->forum->id;
        $record->userid = $this->student->id;
        $this->studentdiscussion = self::getDataGenerator()->get_plugin_generator(
            'mod_'.$this->forumtype)->create_discussion($record);

        $this->component = local_content::component_instance($this->forumtype);
    }

    private function assert_content_items_contain_discussion_post(array $items, $discussionid) {
        global $DB;

        $post = $DB->get_record($this->forumtype.'_posts', ['discussion' => $discussionid, 'parent' => 0]);
        $this->assert_content_items_contain_item($items,
            $post->id, $this->forumtype, $this->forumtype.'_posts', 'message');
    }

    private function assert_content_items_not_contain_discussion_post(array $items, $discussionid) {
        global $DB;

        $post = $DB->get_record($this->forumtype.'_posts', ['discussion' => $discussionid, 'parent' => 0]);
        $this->assert_content_items_not_contain_item($items,
            $post->id, $this->forumtype, $this->forumtype.'_posts', 'message');
    }

    public function test_get_discussion_html_content_items() {
        $contentitems = \phpunit_util::call_internal_method(
            $this->component, 'get_discussion_html_content_items', [
            $this->course->id, $this->forum->id
            ],
            get_class($this->component)
        );

        $this->assert_content_items_contain_discussion_post($contentitems, $this->teacherdiscussion->id);
        $this->assert_content_items_not_contain_discussion_post($contentitems, $this->studentdiscussion->id);
    }

    public function test_resolve_module_instance_id_from_forum() {
        $component = new hsuforum_component();
        $instanceid = $component->resolve_module_instance_id($this->forumtype, $this->forum->id);
        $this->assertEquals($this->forum->id, $instanceid);
    }

    public function test_resolve_module_instance_id_from_post() {
        global $DB;

        $discussion = $this->studentdiscussion;
        $post = $DB->get_record($this->forumtype.'_posts', ['discussion' => $discussion->id, 'parent' => 0]);
        $component = new hsuforum_component();
        $instanceid = $component->resolve_module_instance_id($this->forumtype.'_posts', $post->id);
        $this->assertEquals($this->forum->id, $instanceid);
    }

    public function test_get_all_course_annotation_maps() {
        global $PAGE, $DB;

        $cis = $this->component->get_annotation_maps($this->course->id);
        $expectedannotation = $this->forumtype.':'.$this->forumtype.':intro:'.$this->forum->id;
        $this->assertEquals($expectedannotation, reset($cis['intros']));
        $this->assertEmpty($cis['posts']);

        // Make sure teacher post shows up in annotation maps.
        $PAGE->set_pagetype('mod-'.$this->forumtype.'-discuss');
        $_GET['d'] = $this->teacherdiscussion->id;
        $cis = $this->component->get_annotation_maps($this->course->id);
        $post = $DB->get_record($this->forumtype.'_posts', ['discussion' => $this->teacherdiscussion->id, 'parent' => 0]);
        $expectedannotation = $this->forumtype.':'.$this->forumtype.'_posts:message:'.$post->id;
        $this->assertEquals($expectedannotation, $cis['posts'][$post->id]);

        // Make sure student post does not show up in annotation maps.
        $_GET['d'] = $this->studentdiscussion->id;
        $cis = $this->component->get_annotation_maps($this->course->id);
        $this->assertEmpty($cis['posts']);
    }

    /**
     * Test if file in use detection is working with this module.
     */
    public function test_check_file_in_use() {
        $context = \context_module::instance($this->forum->cmid);

        $usedfiles = [];
        $unusedfiles = [];

        // Check the intro.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_hsuforum', $this->forum->id,
            $this->forumtype, 'intro', $this->teacher);

        // Now we are going to setup file associated with a teacher discussion.
        $postid = $this->teacherdiscussion->firstpost;

        // Check embedded post content.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_hsuforum', $postid,
            $this->forumtype . '_posts', 'message', $this->teacher);

        // Add some attached files that are always in use.
        list($file1, $file2) = $this->setup_check_files($context, 'mod_forum', 'attachment', $postid, $this->teacher);
        $usedfiles[] = $file1; // Silly workaround for PHP code checker.
        $usedfiles[] = $file2;

        // Now setup a teacher post on a discussion.
        $forumgen = self::getDataGenerator()->get_plugin_generator('mod_'.$this->forumtype);

        $post = new \stdClass();
        $post->discussion = $this->teacherdiscussion->id;
        $post->userid = $this->teacher->id;
        $post->parent = $this->teacherdiscussion->firstpost;
        $post->messageformat = FORMAT_HTML;
        $teacherpost = $forumgen->create_post($post);

        $postid = $teacherpost->id;

        // Check embedded post content.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_hsuforum', $postid,
            $this->forumtype . '_posts', 'message', $this->teacher);

        // Add some attached files that are always in use.
        list($file1, $file2) = $this->setup_check_files($context, 'mod_forum', 'attachment', $postid, $this->teacher);
        $usedfiles[] = $file1; // Silly workaround for PHP code checker.
        $usedfiles[] = $file2;

        // This will double check that file iterator is working as expected.
        $this->check_file_iterator_exclusion($context, $usedfiles, $unusedfiles);
    }
}
