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
 * Test for file replace webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\abstract_testcase;
use tool_ally\webservice\replace_file;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/abstract_testcase.php');
require_once($CFG->dirroot . '/files/externallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Test for file replace webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class webservice_replace_file_test extends abstract_testcase {

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var stdClass
     */
    private $teacher;

    /**
     * @throws dml_exception
     */
    public function setUp(): void {
        $this->resetAfterTest();

        $datagen = $this->getDataGenerator();

        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);
        $this->assignUserCapability('moodle/course:managefiles', \context_system::instance()->id, $roleid);
        $this->teacher = $datagen->create_user();
        $this->course = $datagen->create_course();

        $datagen->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');
    }

    private function std_img_html() {
        return '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">';
    }

    /**
     * Test the web service.
     */
    public function test_service() {
        $datagen = $this->getDataGenerator();

        $resource = $datagen->create_module('resource', ['course' => $this->course->id]);
        $file = $this->get_resource_file($resource);

        $draftfile = $this->create_draft_file();

        $return = replace_file::service($file->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $file->get_itemid());

        $file = $this->get_resource_file($resource);
        $this->assertSame($file->get_filename(), $draftfile['filename']);
        $this->assertSame($file->get_content(), base64_decode($draftfile['filecontent']));
        // This should test that the userid of the file creator gets copied,
        // but the mod resource generator always sets the userid to null,
        // Can still test it copies the null value correctly though.
        $this->assertSame($file->get_userid(), null);
    }

    public function test_service_invalid_user() {
        $otheruser = $this->getDataGenerator()->create_user();

        $resource    = $this->getDataGenerator()->create_module('resource', ['course' => $this->course->id]);
        $file        = $this->get_resource_file($resource);

        // Can use fake as user check will fail before it is used.
        $fakeitemid = '123';

        $this->expectException(\moodle_exception::class);
        $return = replace_file::service($file->get_pathnamehash(), $otheruser->id, $fakeitemid);
        \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        // Check file has not been changed.
        $newfile = $this->get_resource_file($resource);
        $this->assertInstanceOf(stored_file, $newfile);
        $this->assertSame($file->get_filename(), $newfile->get_filename());
        $this->assertSame($file->get_content(), $newfile->get_content());
    }

    public function test_service_invalid_file() {
        // Can use fake as file check will fail before it is used.
        $fakeitemid = '123';

        $nonexistantfile = 'BADC0FFEE';
        $this->expectException(\moodle_exception::class);
        replace_file::service($nonexistantfile, $this->teacher->id, $fakeitemid);
    }

    /**
     * Test replacing files within label module intro.
     */
    public function test_service_label_html() {
        global $DB;

        $datagen = $this->getDataGenerator();

        $label = $datagen->create_module('label', ['course' => $this->course->id]);
        $context = \context_module::instance($label->cmid);

        $file = $this->create_test_file($context->id, 'mod_label', 'intro');

        $dobj = (object) [
            'id' => $label->id
        ];
        $dobj->intro = '<p>Test label text '.$this->std_img_html().'</p>';
        $DB->update_record('label', $dobj);

        $draftfile = $this->create_draft_file();

        $return = replace_file::service($file->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $file->get_itemid());

        $label = $DB->get_record('label', ['id' => $label->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $label->intro);
        $this->assertStringContainsString('red%20dot.png', $label->intro);
    }

    /**
     * Test replacing files within page module intro.
     */
    public function test_service_page_html() {
        global $DB;

        $datagen = $this->getDataGenerator();

        $page = $datagen->create_module('page', ['course' => $this->course->id]);
        $context = \context_module::instance($page->cmid);

        $introfile = $this->create_test_file($context->id, 'mod_page', 'intro');
        $contentfile = $this->create_test_file($context->id, 'mod_page', 'content');

        $dobj = (object) [
            'id' => $page->id
        ];
        $dobj->intro = '<p>Test intro text '.$this->std_img_html().'</p>';
        $dobj->content = '<div>Test content text</div>'.$this->std_img_html();
        $DB->update_record('page', $dobj);

        $this->replace_file($introfile);

        // Make sure only the intro field was updated in the page module instance.
        $page = $DB->get_record('page', ['id' => $page->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $page->intro);
        $this->assertStringContainsString('red%20dot.png', $page->intro);
        $this->assertStringContainsString('gd%20logo.png', $page->content);
        $this->assertStringNotContainsString('red%20dot.png', $page->content);

        $this->replace_file($contentfile);

        // Make sure that the content field was update in the page module instance.
        $page = $DB->get_record('page', ['id' => $page->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $page->content);
        $this->assertStringContainsString('red%20dot.png', $page->content);
    }

    /**
     * Test replacing files within course summary.
     */
    public function test_service_course_html() {
        global $DB;

        $context = \context_course::instance($this->course->id);
        $file = $this->create_test_file($context->id, 'course', 'summary');

        $dobj = (object) [
            'id' => $this->course->id
        ];
        $dobj->summary = '<p>Course summary text '.$this->std_img_html().'</p>';
        $DB->update_record('course', $dobj);

        $draftfile = $this->create_draft_file();

        $return = replace_file::service($file->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $file->get_itemid());

        $course = $DB->get_record('course', ['id' => $this->course->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $course->summary);
        $this->assertStringContainsString('red%20dot.png', $course->summary);
    }

    /**
     * Test replacing files within course section html.
     */
    public function test_service_course_section_html() {
        global $DB;

        $datagen = $this->getDataGenerator();

        $course = (object) ['numsections' => 2];
        $course = $datagen->create_course($course, ['createsections' => true]);

        $datagen->enrol_user($this->teacher->id, $course->id, 'editingteacher');

        $context = \context_course::instance($course->id);
        $file = $this->create_test_file($context->id, 'course', 'section');

        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 1]);
        $section->summary = '<span>Course section text '.$this->std_img_html().'</span>';
        $DB->update_record('course_sections', $section);
        $draftfile = $this->create_draft_file();

        $return = replace_file::service($file->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $file->get_itemid());

        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 1]);
        $this->assertStringNotContainsString('gd%20logo.png', $section->summary);
        $this->assertStringContainsString('red%20dot.png', $section->summary);
    }

    /**
     * Test replacing files within course section html.
     */
    public function test_service_block_html() {
        global $DB;

        $configdata = (object) [
            'text' => '',
            'title' => 'test block',
            'format' => FORMAT_HTML
        ];

        $time = new \DateTime("now", \core_date::get_user_timezone_object());

        $blockinsert = (object) [
            'blockname' => 'html',
            'parentcontextid' => \context_course::instance($this->course->id)->id,
            'pagetypepattern' => 'course-view-*',
            'defaultregion' => 'side-pre',
            'defaultweight' => 1,
            'configdata' => base64_encode(serialize($configdata)),
            'showinsubcontexts' => 1,
            'timecreated' => $time->getTimestamp(),
            'timemodified' => $time->getTimestamp()
        ];
        $blockid = $DB->insert_record('block_instances', $blockinsert);
        $block = $DB->get_record('block_instances', ['id' => $blockid]);

        $context = \context_block::instance($block->id);
        $file = $this->create_test_file($context->id, 'block_html', 'content');

        $configdata = (object) [
            'text' => '<img src="@@PLUGINFILE@@/gd logo.png" alt="" width="100" height="100">',
            'title' => 'test block',
            'format' => FORMAT_HTML
        ];
        $block->configdata = base64_encode(serialize($configdata));

        $DB->update_record('block_instances', $block);

        $draftfile = $this->create_draft_file();

        $return = replace_file::service($file->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $file->get_itemid());

        $block = $DB->get_record('block_instances', ['id' => $block->id]);
        $blockconfig = unserialize(base64_decode($block->configdata));
        $blockhtml = $blockconfig->text;
        $this->assertStringNotContainsString('gd logo.png', $blockhtml);
        $this->assertStringContainsString('red dot.png', $blockhtml);
    }

    /**
     * Replace file.
     * @param stored_file $originalfile
     * @param stdClass | bool $user
     * @throws invalid_response_exception
     * @throws moodle_exception
     */
    protected function replace_file(\stored_file $originalfile, $user = null) {
        if (empty($user)) {
            $user = $this->teacher;
        }
        $draftfile = $this->create_draft_file();
        $return = replace_file::service($originalfile->get_pathnamehash(), $user->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);
        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $originalfile->get_itemid());
    }

    /**
     * Test replacing files within forum module intro / discussion / posts.
     */
    public function test_service_forum_html($forumtype = 'forum') {
        global $DB;

        $datagen = $this->getDataGenerator();

        $forum = $datagen->create_module($forumtype, ['course' => $this->course->id]);
        $context = \context_module::instance($forum->cmid);
        $forumfile = $this->create_test_file($context->id, 'mod_'.$forumtype, 'intro');
        $dobj = (object) [
            'id' => $forum->id
        ];
        $dobj->intro = 'forum intro '.$this->std_img_html();
        $dobj->content = '<p>Forum content '.$this->std_img_html().'</p>';
        $DB->update_record($forumtype, $dobj);

        $fdg = $datagen->get_plugin_generator('mod_'.$forumtype);

        // Create discussion / post.
        $record = new \stdClass();
        $record->course = $this->course->id;
        $record->userid = $this->teacher->id;
        $record->forum = $forum->id;
        // Add file to discussion post.
        $discussion = $fdg->create_discussion($record);
        $discussionpost = $DB->get_record($forumtype.'_posts', ['discussion' => $discussion->id]);
        $discussionfile = $this->create_test_file($context->id, 'mod_'.$forumtype, 'post', $discussionpost->id);
        $discussionpost->message = $this->std_img_html();
        $DB->update_record($forumtype.'_posts', $discussionpost);

        // Create post replying to discussion.
        $record = new \stdClass();
        $record->discussion = $discussionpost->discussion;
        $record->parent = $discussionpost->id;
        $record->userid = $this->teacher->id;
        $post = $fdg->create_post($record);
        // Add file to reply.
        $postfile = $this->create_test_file($context->id, 'mod_'.$forumtype, 'post', $post->id);
        $post->message = '<div>'.$this->std_img_html().'</div>';
        $DB->update_record($forumtype.'_posts', $post);

        // Replace main forum file.
        $this->replace_file($forumfile);

        // Ensure that forum main record has had file link replaced in HTML.
        $forum = $DB->get_record($forumtype, ['id' => $forum->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $forum->intro);
        $this->assertStringContainsString('red%20dot.png', $forum->intro);

        // Ensure that both discussion post and reply post have NOT had file link replaced in HTML.
        $discussionpost = $DB->get_record($forumtype.'_posts', ['id' => $discussionpost->id, 'parent' => 0]);
        $post = $DB->get_record($forumtype.'_posts', ['id' => $post->id]);
        $this->assertStringContainsString('gd%20logo.png', $discussionpost->message);
        $this->assertStringNotContainsString('red%20dot.png', $discussionpost->message);
        $this->assertStringContainsString('gd%20logo.png', $post->message);
        $this->assertStringNotContainsString('red%20dot.png', $post->message);

        // Replace discussion file.
        $this->replace_file($discussionfile);

        // Ensure that discussion post has had file link replaced but reply post has not.
        $discussionpost = $DB->get_record($forumtype.'_posts', ['id' => $discussionpost->id, 'parent' => 0]);
        $post = $DB->get_record($forumtype.'_posts', ['id' => $post->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $discussionpost->message);
        $this->assertStringContainsString('red%20dot.png', $discussionpost->message);
        $this->assertStringContainsString('gd%20logo.png', $post->message);
        $this->assertStringNotContainsString('red%20dot.png', $post->message);

        // Replace reply post file.
        $this->replace_file($postfile);

        // Ensure that reply post has had file links replaced.
        $post = $DB->get_record($forumtype.'_posts', ['id' => $post->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $post->message);
        $this->assertStringContainsString('red%20dot.png', $post->message);
    }

    /**
     * Test replacing files within hsuforum module intro / discussion / posts.
     */
    public function test_service_hsuforum_html() {
        global $CFG;
        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            $this->test_service_forum_html('hsuforum');
        }
    }

    /**
     * Test replacing files within questions.
     */
    public function test_service_question_html() {
        global $DB;

        $datagen = $this->getDataGenerator();
        $qgen = $datagen->get_plugin_generator('core_question');

        $cat = $qgen->create_question_category();
        $question = $qgen->create_question('shortanswer', null, array('category' => $cat->id));
        $questionrow = $DB->get_record('question', ['id' => $question->id]);

        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $this->course->id));
        $context = \context_course::instance($this->course->id);
        quiz_add_quiz_question($question->id, $quiz);

        $qfile = $this->create_test_file($context->id, 'question', 'questiontext', $question->id);
        $questionrow->questiontext = 'Question text '.$this->std_img_html();
        $DB->update_record('question', $questionrow);

        // Replace file.
        $this->replace_file($qfile);

        $questionrow = $DB->get_record('question', ['id' => $question->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $questionrow->questiontext);
        $this->assertStringContainsString('red%20dot.png', $questionrow->questiontext);
    }

    /**
     * Assert file processed in text.
     * @param string $text
     */
    private function assert_file_processed_in_text($text) {
        $this->assertStringNotContainsString('gd%20logo.png', $text);
        $this->assertStringContainsString('red%20dot.png', $text);
    }

    /**
     * Assert file not processed in text.
     * @param string $text
     */
    private function assert_file_not_processed_in_text($text) {
        $this->assertStringContainsString('gd%20logo.png', $text);
        $this->assertStringNotContainsString('red%20dot.png', $text);
    }

    /**
     * Test replacing files within questions.
     */
    public function test_service_question_html_multichoice() {
        global $DB;

        $datagen = $this->getDataGenerator();
        $qgen = $datagen->get_plugin_generator('core_question');

        $cat = $qgen->create_question_category();
        $question = $qgen->create_question('multichoice', null, array('category' => $cat->id));
        $questionrow = $DB->get_record('question', ['id' => $question->id]);

        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $this->course->id));
        $context = \context_course::instance($this->course->id);
        quiz_add_quiz_question($question->id, $quiz);

        $qfile = $this->create_test_file($context->id, 'question', 'questiontext', $question->id);
        $questionrow->questiontext = '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">';
        $DB->update_record('question', $questionrow);

        // Replace file.
        $this->replace_file($qfile);

        $questionrow = $DB->get_record('question', ['id' => $question->id]);
        $this->assert_file_processed_in_text($questionrow->questiontext);

        $combinedfeedback = $DB->get_record('qtype_multichoice_options', ['questionid' => $question->id]);
        $cfid = $combinedfeedback->id;

        // Test multichoice combined feedback.
        $combinedfeedback = (object) [
            'id' => $cfid,
            'questionid' => $question->id,
            'correctfeedback' => '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">',
            'correctfeedbackformat' => FORMAT_HTML,
            'partiallycorrectfeedback' => '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">',
            'partiallycorrectfeedbackformat' => FORMAT_HTML,
            'incorrectfeedback' => '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">',
            'incorrectfeedbackformat' => FORMAT_HTML
        ];
        $DB->update_record('qtype_multichoice_options', $combinedfeedback);

        $correctfeedbackfile = $this->create_test_file(
                $context->id, 'question', 'correctfeedback', $question->id);
        $partiallycorrectfeedbackfile = $this->create_test_file(
            $context->id, 'question', 'partiallycorrectfeedback', $question->id);
        $incorrectfeedbackfile = $this->create_test_file(
                $context->id, 'question', 'incorrectfeedback', $question->id);

        $combinedfeedback = $DB->get_record('qtype_multichoice_options', ['id' => $cfid]);
        $this->assert_file_not_processed_in_text($combinedfeedback->correctfeedback);
        $this->assert_file_not_processed_in_text($combinedfeedback->partiallycorrectfeedback);
        $this->assert_file_not_processed_in_text($combinedfeedback->incorrectfeedback);

        $this->replace_file($correctfeedbackfile);
        $combinedfeedback = $DB->get_record('qtype_multichoice_options', ['id' => $cfid]);
        $this->assert_file_processed_in_text($combinedfeedback->correctfeedback);
        $this->assert_file_not_processed_in_text($combinedfeedback->partiallycorrectfeedback);
        $this->assert_file_not_processed_in_text($combinedfeedback->incorrectfeedback);

        $this->replace_file($partiallycorrectfeedbackfile);
        $combinedfeedback = $DB->get_record('qtype_multichoice_options', ['id' => $cfid]);
        $this->assert_file_processed_in_text($combinedfeedback->correctfeedback);
        $this->assert_file_processed_in_text($combinedfeedback->partiallycorrectfeedback);
        $this->assert_file_not_processed_in_text($combinedfeedback->incorrectfeedback);

        $this->replace_file($incorrectfeedbackfile);
        $combinedfeedback = $DB->get_record('qtype_multichoice_options', ['id' => $cfid]);
        $this->assert_file_processed_in_text($combinedfeedback->correctfeedback);
        $this->assert_file_processed_in_text($combinedfeedback->partiallycorrectfeedback);
        $this->assert_file_processed_in_text($combinedfeedback->incorrectfeedback);

        // Test multiple choice answers.
        // Make sure each file replace does not affect other fields or answer rows.
        $ans = (object) [
            'question' => $question->id,
            'answer' => 'Answer : <img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">',
            'answerformat' => FORMAT_HTML,
            'fraction' => 0,
            'feedback' => 'Feedback : <img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">',
            'feedbackformat' => FORMAT_HTML
        ];
        $ans1id = $DB->insert_record('question_answers', $ans);
        $ans2id = $DB->insert_record('question_answers', $ans);
        $ans1answerfile = $this->create_test_file(
            $context->id, 'question', 'answer', $ans1id);
        $ans1feedbackfile = $this->create_test_file(
            $context->id, 'question', 'answerfeedback', $ans1id);
        $ans2answerfile = $this->create_test_file(
            $context->id, 'question', 'answer', $ans2id);
        $ans2feedbackfile = $this->create_test_file(
            $context->id, 'question', 'answerfeedback', $ans2id);

        $ans1 = $DB->get_record('question_answers', ['id' => $ans1id]);
        $ans2 = $DB->get_record('question_answers', ['id' => $ans2id]);
        $this->assert_file_not_processed_in_text($ans1->answer);
        $this->assert_file_not_processed_in_text($ans1->feedback);
        $this->assert_file_not_processed_in_text($ans2->answer);
        $this->assert_file_not_processed_in_text($ans2->feedback);

        $this->replace_file($ans1answerfile);
        $ans1 = $DB->get_record('question_answers', ['id' => $ans1id]);
        $ans2 = $DB->get_record('question_answers', ['id' => $ans2id]);
        $this->assert_file_processed_in_text($ans1->answer);
        $this->assert_file_not_processed_in_text($ans1->feedback);
        $this->assert_file_not_processed_in_text($ans2->answer);
        $this->assert_file_not_processed_in_text($ans2->feedback);

        $this->replace_file($ans1feedbackfile);
        $ans1 = $DB->get_record('question_answers', ['id' => $ans1id]);
        $ans2 = $DB->get_record('question_answers', ['id' => $ans2id]);
        $this->assert_file_processed_in_text($ans1->answer);
        $this->assert_file_processed_in_text($ans1->feedback);
        $this->assert_file_not_processed_in_text($ans2->answer);
        $this->assert_file_not_processed_in_text($ans2->feedback);

        $this->replace_file($ans2answerfile);
        $ans1 = $DB->get_record('question_answers', ['id' => $ans1id]);
        $ans2 = $DB->get_record('question_answers', ['id' => $ans2id]);
        $this->assert_file_processed_in_text($ans1->answer);
        $this->assert_file_processed_in_text($ans1->feedback);
        $this->assert_file_processed_in_text($ans2->answer);
        $this->assert_file_not_processed_in_text($ans2->feedback);

        $this->replace_file($ans2feedbackfile);
        $ans1 = $DB->get_record('question_answers', ['id' => $ans1id]);
        $ans2 = $DB->get_record('question_answers', ['id' => $ans2id]);
        $this->assert_file_processed_in_text($ans1->answer);
        $this->assert_file_processed_in_text($ans1->feedback);
        $this->assert_file_processed_in_text($ans2->answer);
        $this->assert_file_processed_in_text($ans2->feedback);
    }

    public function test_service_qtype_ddmatch_html() {
        global $CFG, $DB, $USER;

        if (!file_exists($CFG->dirroot.'/question/type/ddmatch')) {
            return;
        }

        require_once($CFG->libdir . '/questionlib.php');

        $datagen = $this->getDataGenerator();
        $qgen = $datagen->get_plugin_generator('core_question');

        $cat = $qgen->create_question_category();

        $this->setAdminUser();

        // Sadly we can't use a question generator for ddmatch because the qtype_ddmatch_test_helper class is missing
        // a get_test_questions method.
        $questionid = $DB->insert_record('question', (object) [
            'category' => $cat->id,
            'parent' => 0,
            'name' => 'DD match test',
            'questiontext' => 'Question text '.$this->std_img_html(),
            'questiontextformat' => FORMAT_HTML,
            'generalfeedback' => 'General feedabck text '.$this->std_img_html(),
            'generalfeedbackformat' => FORMAT_HTML,
            'defaultmark' => 1,
            'penalty' => 1,
            'qtype' => 'ddmatch',
            'length' => 1,
            'timecreated' => time(),
            'timemodified' => time(),
            'createdby' => $USER->id,
            'modifiedby' => $USER->id,
            'stamp' => make_unique_id_code(),
        ]);
        $bankentryid = $DB->insert_record('question_bank_entries', (object) [
            'questioncategoryid' => $cat->id,
            'ownerid' => $USER->id,
        ]);
        $DB->insert_record('question_versions', (object) [
            'questionbankentryid' => $bankentryid,
            'version' => 0,
            'questionid' => $questionid,
        ]);
        $question = $DB->get_record('question', ['id' => $questionid]);

        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $context = \context_course::instance($this->course->id);
        quiz_add_quiz_question($questionid, $quiz);

        $qfile = $this->create_test_file($context->id, 'question', 'questiontext', $questionid);

        // Replace question file.
        $this->replace_file($qfile);

        // Make sure question has been processed.
        $question = $DB->get_record('question', ['id' => $questionid]);
        $this->assertStringNotContainsString('gd%20logo.png', $question->questiontext);
        $this->assertStringContainsString('red%20dot.png', $question->questiontext);

        // Create sub questions.
        $subqa = (object) [
            'questionid' => $questionid,
            'questiontext' => 'Subquestion A Question text'.$this->std_img_html(),
            'questiontextformat' => FORMAT_HTML,
            'answertext' => 'Subquestion A Answer text'.$this->std_img_html(),
            'answertextformat' => FORMAT_HTML
        ];
        $subqaid = $DB->insert_record('qtype_ddmatch_subquestions', $subqa);
        $subqarow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqaid]);
        $subqaquestionfile = $this->create_test_file(
                $context->id, 'qtype_ddmatch', 'subquestion', $subqarow->id);
        $subqaanswerfile = $this->create_test_file(
                $context->id, 'qtype_ddmatch', 'subanswer', $subqarow->id);

        $subqb = (object) [
            'questionid' => $questionid,
            'questiontext' => 'Subquestion B '.$this->std_img_html(),
            'questiontextformat' => FORMAT_HTML,
            'answertext' => 'Subquestion B Answer text'.$this->std_img_html(),
            'answertextformat' => FORMAT_HTML
        ];
        $subqbid = $DB->insert_record('qtype_ddmatch_subquestions', $subqb);
        $subqbrow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqbid]);
        $subqbquestionfile = $this->create_test_file(
            $context->id, 'qtype_ddmatch', 'subquestion', $subqbrow->id);
        $subqbanswerfile = $this->create_test_file(
            $context->id, 'qtype_ddmatch', 'subanswer', $subqbrow->id);

        // Test replacing file in just one questions question field.
        $this->replace_file($subqaquestionfile);
        $subqarow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqaid]);
        $subqbrow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqbid]);
        $this->assert_file_processed_in_text($subqarow->questiontext);
        $this->assert_file_not_processed_in_text($subqarow->answertext);
        $this->assert_file_not_processed_in_text($subqbrow->questiontext);
        $this->assert_file_not_processed_in_text($subqbrow->answertext);

        // Test replacing files in just one questions question and answer field.
        $this->replace_file($subqaanswerfile);
        $subqarow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqaid]);
        $subqbrow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqbid]);
        $this->assert_file_processed_in_text($subqarow->questiontext);
        $this->assert_file_processed_in_text($subqarow->answertext);
        $this->assert_file_not_processed_in_text($subqbrow->questiontext);
        $this->assert_file_not_processed_in_text($subqbrow->answertext);

        // Test replacing files for two questions for both question and answer field.
        $this->replace_file($subqbquestionfile);
        $this->replace_file($subqbanswerfile);
        $subqarow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqaid]);
        $subqbrow = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $subqbid]);
        $this->assert_file_processed_in_text($subqarow->questiontext);
        $this->assert_file_processed_in_text($subqarow->answertext);
        $this->assert_file_processed_in_text($subqbrow->questiontext);
        $this->assert_file_processed_in_text($subqbrow->answertext);

        // Create match options.
        $options = (object) [
            'questionid' => $questionid,
            'suffleanswers' => 1,
            'correctfeedback' => '<span>Correct feedback '.$this->std_img_html().'</span>',
            'partiallycorrectfeedback' => '<p>Partially Correct feedback</p>'.$this->std_img_html(),
            'incorrectfeedback' => '<div>Incorrect feedback</div>'.$this->std_img_html(),
        ];
        $optionsrowid = $DB->insert_record('qtype_ddmatch_options', $options);
        $optionsrow = $DB->get_record('qtype_ddmatch_options', ['id' => $optionsrowid]);
        // Create files for optionsrow.
        $correctfeedbackfile = $this->create_test_file(
                $context->id, 'question', 'correctfeedback', $question->id);
        $partiallycorrectfeedbackfile = $this->create_test_file(
                $context->id, 'question', 'partiallycorrectfeedback', $question->id);
        $incorrectfeedbackfile = $this->create_test_file(
                $context->id, 'question', 'incorrectfeedback', $question->id);

        // Test replacing file in correct feedback text.
        $this->replace_file($correctfeedbackfile);
        $optionsrow = $DB->get_record('qtype_ddmatch_options', ['id' => $optionsrowid]);
        $this->assert_file_processed_in_text($optionsrow->correctfeedback);
        $this->assert_file_not_processed_in_text($optionsrow->partiallycorrectfeedback);
        $this->assert_file_not_processed_in_text($optionsrow->incorrectfeedback);

        // Test replacing file in partially correct feedback text.
        $this->replace_file($partiallycorrectfeedbackfile);
        $optionsrow = $DB->get_record('qtype_ddmatch_options', ['id' => $optionsrowid]);
        $this->assert_file_processed_in_text($optionsrow->correctfeedback);
        $this->assert_file_processed_in_text($optionsrow->partiallycorrectfeedback);
        $this->assert_file_not_processed_in_text($optionsrow->incorrectfeedback);

        // Test replacing file in incorrect feedback text.
        $this->replace_file($incorrectfeedbackfile);
        $optionsrow = $DB->get_record('qtype_ddmatch_options', ['id' => $optionsrowid]);
        $this->assert_file_processed_in_text($optionsrow->correctfeedback);
        $this->assert_file_processed_in_text($optionsrow->partiallycorrectfeedback);
        $this->assert_file_processed_in_text($optionsrow->incorrectfeedback);
    }

    /**
     * Test replacing files within lesson module intro / pages.
     */
    public function test_service_lesson_html() {
        global $DB;

        // Lesson intro file replacement testing.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $this->course->id));
        $dobj = (object)[
            'id' => $lesson->id
        ];
        $dobj->intro = '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">';
        $context = \context_module::instance($lesson->cmid);
        $DB->update_record('lesson', $dobj);

        $lfile = $this->create_test_file($context->id, 'mod_lesson', 'intro');

        // Replace file.
        $this->replace_file($lfile);

        $lessonrow = $DB->get_record('lesson', ['id' => $lesson->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $lessonrow->intro);
        $this->assertStringContainsString('red%20dot.png', $lessonrow->intro);

        // Lesson page content file replacement testing.
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page = $lessongenerator->create_content($lesson, array('title' => 'Simple page'));
        $dobj = (object)[
            'id' => $page->id
        ];
        $dobj->contents = '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">';
        $DB->update_record('lesson_pages', $dobj);

        $pfile = $this->create_test_file($context->id, 'mod_lesson', 'page_contents', $page->id);

        // Replace file.
        $this->replace_file($pfile);

        $pagerow = $DB->get_record('lesson_pages', ['id' => $page->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $pagerow->contents);
        $this->assertStringContainsString('red%20dot.png', $pagerow->contents);
    }

    /**
     * Test replacing files within glossary module intro / pages.
     */
    public function test_service_glossary_html() {
        global $DB;

        // Glossary intro file replacement testing.
        $this->setAdminUser();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $this->course->id));
        $dobj = (object)[
            'id' => $glossary->id
        ];
        $dobj->intro = '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">';
        $context = \context_module::instance($glossary->cmid);
        $DB->update_record('glossary', $dobj);

        $gfile = $this->create_test_file($context->id, 'mod_glossary', 'intro');

        // Replace file.
        $this->replace_file($gfile);

        $glossaryrow = $DB->get_record('glossary', ['id' => $glossary->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $glossaryrow->intro);
        $this->assertStringContainsString('red%20dot.png', $glossaryrow->intro);

        // Glossary entry file replacement testing.
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $entry = $glossarygenerator->create_content($glossary);
        $dobj = (object)[
            'id' => $entry->id,
            'definition' => '<img src="@@PLUGINFILE@@/gd%20logo.png" alt="" width="100" height="100">'
        ];
        $DB->update_record('glossary_entries', $dobj);

        $efile = $this->create_test_file($context->id, 'mod_glossary', 'entry', $entry->id);

        // Replace file.
        $this->replace_file($efile);

        $entryrow = $DB->get_record('glossary_entries', ['id' => $entry->id]);
        $this->assertStringNotContainsString('gd%20logo.png', $entryrow->definition);
        $this->assertStringContainsString('red%20dot.png', $entryrow->definition);
    }

    /**
     * Test replacing file where filename already exists.
     */
    public function test_service_replace_existing_filename() {
        global $DB;

        $datagen = $this->getDataGenerator();

        $label = $datagen->create_module('label', ['course' => $this->course->id]);
        $context = \context_module::instance($label->cmid);

        $filetoreplacename = 'file to replace.png';
        $filetoreplace = $this->create_test_file($context->id, 'mod_label', 'intro', 0, $filetoreplacename);

        $filename = 'name to increment.png';
        $this->create_test_file($context->id, 'mod_label', 'intro', 0, $filename);

        $dobj = (object) [
            'id' => $label->id
        ];
        $dobj->intro = '<img src="@@PLUGINFILE@@/'.rawurlencode($filename).'" alt="" width="100" height="100">'.
                '<img src="@@PLUGINFILE@@/'.rawurlencode($filetoreplacename).'" alt="" width="100" height="100">';
        $DB->update_record('label', $dobj);

        // Draft file will have the same filename.
        $draftfile = $this->create_draft_file($filename);

        $return = replace_file::service($filetoreplace->get_pathnamehash(), $this->teacher->id, $draftfile['itemid']);
        $return = \external_api::clean_returnvalue(replace_file::service_returns(), $return);

        $this->assertSame($return['success'], true);
        $this->assertNotSame($return['newid'], $filetoreplace->get_itemid());

        $label = $DB->get_record('label', ['id' => $label->id]);
        $this->assertStringContainsString(rawurlencode($filename), $label->intro);
        $this->assertStringContainsString(rawurlencode('name to increment (1).png'), $label->intro);
        $this->assertStringNotContainsString(rawurlencode($filetoreplacename), $label->intro);
    }
}
