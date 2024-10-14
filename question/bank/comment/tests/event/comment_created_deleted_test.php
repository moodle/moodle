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

namespace qbank_comment\event;

use advanced_testcase;
use cache;
use comment;
use context;
use context_course;
use context_module;
use core_question_generator;
use stdClass;

/**
 * Event tests for question comments.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class comment_created_deleted_test extends advanced_testcase {

    /** @var stdClass Keeps course object */
    private $course;

    /** @var context Keeps context */
    private $context;

    /** @var stdClass Keeps question object */
    private $questiondata;

    /** @var stdClass Keeps comment object */
    private $comment;

    /**
     * Setup test data.
     */
    public function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');
        parent::setUp();

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $generator->get_plugin_generator('core_question');

        // Create a course.
        $this->course = $generator->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $this->course->id]);
        $this->context = context_module::instance($qbank->cmid);

        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($this->context);
        $cat = question_get_default_category($contexts->lowest()->id, true);
        $this->questiondata = $questiongenerator->create_question('numerical', null,
                ['name' => 'Example question', 'category' => $cat->id]);

        // Ensure the question is not in the cache.
        $cache = cache::make('core', 'questiondata');
        $cache->delete($this->questiondata->id);

        // Comment on question.
        $args = new stdClass;
        $args->context = $this->context;
        $args->course = $this->course;
        $args->area = 'question';
        $args->itemid = $this->questiondata->id;
        $args->component = 'qbank_comment';
        $args->linktext = get_string('commentheader', 'qbank_comment');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $this->comment = new comment($args);
    }

    /**
     * Test comment_created event.
     */
    public function test_comment_created(): void {
        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $this->comment->add('New comment');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\qbank_comment\event\comment_created', $event);
        $this->assertEquals($this->context, $event->get_context());
        $this->assertStringContainsString('\'qbank_comment\' for the question with ID \''.$this->questiondata->id.'\'',
                $event->get_description());
    }

    /**
     * Test comment_created event.
     */
    public function test_comment_deleted(): void {
        // Triggering and capturing the event.
        $newcomment = $this->comment->add('New comment to delete');
        $sink = $this->redirectEvents();
        $this->comment->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\qbank_comment\event\comment_deleted', $event);
        $this->assertEquals($this->context, $event->get_context());
        $this->assertStringContainsString('\'qbank_comment\' for the question with ID \''.$this->questiondata->id.'\'',
                $event->get_description());
    }
}
