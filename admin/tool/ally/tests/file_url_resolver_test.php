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
 * Test file URL resolver.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\file_url_resolver;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test file URL resolver.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_url_resolver_test extends abstract_testcase {

    private $course;

    /**
     * @var tool_ally_generator
     */
    private $generator;

    protected function setUp(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());

        $this->course    = $this->getDataGenerator()->create_course();
        $this->generator = $this->getDataGenerator()->get_plugin_generator('tool_ally');
    }

    /**
     * Test default URL resolution.
     */
    public function test_resolve_default() {
        global $CFG;

        $resource = $this->getDataGenerator()->create_module('resource', ['course' => $this->course->id]);
        $file     = $this->get_resource_file($resource);

        $resolver = new file_url_resolver();
        $url      = $resolver->resolve_url($file);

        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertEquals($CFG->wwwroot.'/mod/resource/view.php?id='.$resource->cmid, $url->out());
    }

    /**
     * Test forum post URL resolution.
     */
    public function test_resolve_forum_post() {
        global $USER, $DB;

        $forum      = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id]);
        $modcontext = \context_module::instance($forum->cmid);
        $draft      = $this->generator->create_draft_file();

        /** @var \mod_forum_generator $generator */
        $generator  = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $generator->create_discussion([
            'forum'  => $forum->id,
            'course' => $this->course->id,
            'userid' => $USER->id,
            'itemid' => $draft->get_itemid()
        ]);

        $post = $DB->get_record('forum_posts', ['discussion' => $discussion->id]);
        $file = get_file_storage()->get_file($modcontext->id, 'mod_forum', 'post',
            $post->id, $draft->get_filepath(), $draft->get_filename());

        $resolver = new file_url_resolver();
        $url      = $resolver->resolve_url($file);

        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertTrue($url->compare(
            new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id], 'p'.$post->id)
        ));
    }

    /**
     * Test question URL resolution.
     */
    public function test_resolve_question() {
        $context = \context_course::instance($this->course->id);
        $draft   = $this->generator->create_draft_file();

        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat       = $generator->create_question_category(['contextid' => $context->id]);
        $question  = $generator->create_question('shortanswer', null, [
            'category'     => $cat->id,
            'questiontext' => [
                'text'   => 'Text.',
                'format' => FORMAT_HTML,
                'itemid' => $draft->get_itemid()
            ]
        ]);

        $file = get_file_storage()->get_file($context->id, 'question', 'questiontext', $question->id,
            $draft->get_filepath(), $draft->get_filename());

        $resolver = new file_url_resolver();
        $url      = $resolver->resolve_url($file);

        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertTrue($url->compare(
            new \moodle_url('/question/question.php', ['courseid' => $this->course->id, 'id' => $question->id])
        ));
    }
}
