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
 * Test for course content webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\course_content;
use tool_ally\models\component;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for course content webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_course_content_test extends abstract_testcase {

    private function get_forum_expectations($course, $forumtype = 'forum') {
        global $USER;

        $forumintro = '<p>My original intro content</p>';
        $forum = $this->getDataGenerator()->create_module($forumtype,
            ['course' => $course->id, 'intro' => $forumintro, 'introformat' => FORMAT_HTML]);
        $expectedforum = new component(
            $forum->id,
            $forumtype,
            $forumtype,
            'intro',
            $course->id,
            $forum->timemodified,
            $forum->introformat,
            $forum->name
        );

        // Forum posts not created by teacher / admin are not included in results.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a discussion by regular user.
        $record = new \stdClass();
        $record->forum = $forum->id;
        $record->userid = $user->id;
        $record->course = $course->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_discussion($record);

        // Add a reply by regular user.
        $posttitle = 'My post title';
        $postmessage = 'My post message';
        $record = new \stdClass();
        $record->discussion = $discussion->id;
        $record->userid = $user->id;
        $record->subject = $posttitle;
        $record->message = $postmessage;
        $record->messageformat = FORMAT_HTML;
        $post = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_post($record);

        $unexpectedpost = new component(
            $post->id,
            $forumtype,
            $forumtype.'_posts',
            'message',
            $course->id,
            $post->modified,
            $post->messageformat,
            $posttitle
        );

        // Add a discussion / post by admin user - should show up in results.
        $this->setAdminUser();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->forum = $forum->id;
        $record->userid = $USER->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_discussion($record);

        $posttitle = 'My post title';
        $postmessage = 'My post message';
        $record = new \stdClass();
        $record->discussion = $discussion->id;
        $record->userid = $USER->id;
        $record->subject = $posttitle;
        $record->message = $postmessage;
        $record->messageformat = FORMAT_HTML;
        $post = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_post($record);

        $expectedpost = new component(
            $post->id,
            $forumtype,
            $forumtype.'_posts',
            'message',
            $course->id,
            $post->modified,
            $post->messageformat,
            $posttitle
        );

        return [$expectedforum, $expectedpost, $unexpectedpost];
    }

    /**
     * Test the web service when used to get course content items.
     */
    public function test_service() {
        global $DB, $CFG;

        $this->resetAfterTest();
        $roleid = $this->assignUserCapability('moodle/course:view', \context_system::instance()->id);
        $this->assignUserCapability('moodle/course:viewhiddencourses', \context_system::instance()->id, $roleid);

        $coursesummary = '<p>My course summary</p>';
        $course = $this->getDataGenerator()->create_course(
                ['summary' => $coursesummary, 'summaryformat' => FORMAT_HTML]);
        $expectedcourse = new component(
            $course->id,
            'course',
            'course',
            'summary',
            $course->id,
            $course->timemodified,
            $course->summaryformat,
            $course->fullname
        );

        $section0summary = '<p>First section summary</p>';
        $section = $this->getDataGenerator()->create_course_section(
                ['section' => 0, 'course' => $course->id]);
        $DB->update_record('course_sections', (object) [
            'id' => $section->id,
            'summary' => $section0summary,
            'summaryformat' => FORMAT_HTML
        ]);
        $section = $DB->get_record('course_sections', ['id' => $section->id]);
        $expectedsection = new component(
            $section->id,
            'course',
            'course_sections',
            'summary',
            $course->id,
            $section->timemodified,
            $section->summaryformat,
            'Topic 0' // Default section name for section 0 where no section name set.
        );

        $labelintro = '<p>My original intro content</p>';
        $label = $this->getDataGenerator()->create_module('label',
            ['course' => $course->id, 'intro' => $labelintro, 'introformat' => FORMAT_HTML]);
        $expectedlabel = new component(
            $label->id,
            'label',
            'label',
            'intro',
            $course->id,
            $label->timemodified,
            $label->introformat,
            $label->name
        );

        $assignintro = '<p>My assign original intro content</p>';
        $assign = $this->getDataGenerator()->create_module('assign',
            ['course' => $course->id, 'intro' => $assignintro, 'introformat' => FORMAT_HTML]);
        $expectedassign = new component(
            $assign->id,
            'assign',
            'assign',
            'intro',
            $course->id,
            $assign->timemodified,
            $assign->introformat,
            $assign->name
        );

        list ($expectedforum, $expectedpost, $unexpectedpost) = $this->get_forum_expectations($course);

        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            list ($hsuexpectedforum, $hsuexpectedpost, $hsuunexpectedpost) = $this->get_forum_expectations($course);
        }

        $contents = course_content::service([$course->id]);

        $this->assertTrue(in_array($expectedcourse, $contents));
        $this->assertTrue(in_array($expectedsection, $contents));
        $this->assertTrue(in_array($expectedlabel, $contents));
        $this->assertTrue(in_array($expectedassign, $contents));
        $this->assertTrue(in_array($expectedforum, $contents));
        $this->assertTrue(in_array($expectedpost, $contents));
        $this->assertFalse(in_array($unexpectedpost, $contents));
        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            $this->assertTrue(in_array($hsuexpectedforum, $contents));
            $this->assertTrue(in_array($hsuexpectedpost, $contents));
            $this->assertFalse(in_array($hsuunexpectedpost, $contents));
        }
    }
}
