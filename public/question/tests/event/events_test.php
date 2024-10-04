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
 * Events tests.
 *
 * @package core_question
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\event;

use qtype_description;
use qtype_description_edit_form;
use qtype_description_test_helper;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/editlib.php');

final class events_test extends \advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test the questions imported event.
     * There is no easy way to trigger this event using the API, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_questions_imported(): void {

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $contexts = new \core_question\local\bank\question_edit_contexts(\context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);

        $category = $questiongenerator->create_question_category([
            'name' => 'newcategory',
            'parent' => $defaultcategory->id,
        ]);

        // Log the view of this category.
        $params = [
                'context' => \context_module::instance($quiz->cmid),
                'other' => ['categoryid' => $category->id, 'format' => 'testformat'],
        ];

        $event = \core\event\questions_imported::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\questions_imported', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEquals($category->id, $event->other['categoryid']);
        $this->assertEquals('testformat', $event->other['format']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the questions exported event.
     * There is no easy way to trigger this event using the API, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_questions_exported(): void {

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $contexts = new \core_question\local\bank\question_edit_contexts(\context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);

        $category = $questiongenerator->create_question_category([
            'name' => 'newcategory',
            'parent' => $defaultcategory->id,
        ]);

        // Log the view of this category.
        $params = [
                'context' => \context_module::instance($quiz->cmid),
                'other' => ['categoryid' => $category->id, 'format' => 'testformat'],
        ];

        $event = \core\event\questions_exported::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\questions_exported', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEquals($category->id, $event->other['categoryid']);
        $this->assertEquals('testformat', $event->other['format']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the question created event.
     */
    public function test_question_created(): void {

        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $questiondata = $generator->create_question('description', null, ['category' => $cat->id]);
        $question = \question_bank::load_question($questiondata->id);

        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_created', $event);
        $this->assertEquals($question->id, $event->objectid);
        $this->assertEquals($cat->id, $event->other['categoryid']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the question deleted event.
     */
    public function test_question_deleted(): void {

        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1]);

        $questiondata = $generator->create_question('description', null, ['category' => $cat->id]);
        $question = \question_bank::load_question($questiondata->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        question_delete_question($question->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_deleted', $event);
        $this->assertEquals($question->id, $event->objectid);
        $this->assertEquals($cat->id, $event->other['categoryid']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the question updated event.
     */
    public function test_question_updated(): void {

        global $CFG;
        require_once($CFG->dirroot . '/question/type/description/questiontype.php');
        require_once($CFG->dirroot . '/question/type/edit_question_form.php');
        require_once($CFG->dirroot . '/question/type/description/edit_description_form.php');

        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1]);

        $questiondata = $generator->create_question('description', null, ['category' => $cat->id]);
        $question = \question_bank::load_question($questiondata->id);

        $qtype = new qtype_description();
        $formdata = test_question_maker::get_question_form_data('description');
        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_description_edit_form::mock_submit((array) $formdata);

        $form = qtype_description_test_helper::get_question_editing_form($cat, $questiondata);
        $fromform = $form->get_data();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $question = $qtype->save_question($questiondata, $fromform);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        // Every save is a new question after Moodle 4.0.
        $this->assertInstanceOf('\core\event\question_created', $event);
        $this->assertEquals($question->id, $event->objectid);
        $this->assertEquals($cat->id, $event->other['categoryid']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the question moved event.
     */
    public function test_question_moved(): void {

        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat1 = $generator->create_question_category([
                'name' => 'My category 1', 'sortorder' => 1]);

        $cat2 = $generator->create_question_category([
                'name' => 'My category 2', 'sortorder' => 2]);

        $questiondata = $generator->create_question('description', null, ['category' => $cat1->id]);
        $question = \question_bank::load_question($questiondata->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        question_move_questions_to_category([$question->id], $cat2->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_moved', $event);
        $this->assertEquals($question->id, $event->objectid);
        $this->assertEquals($cat1->id, $event->other['oldcategoryid']);
        $this->assertEquals($cat2->id, $event->other['newcategoryid']);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the question viewed event.
     * There is no external API for viewing the question, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_question_viewed(): void {

        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1]);

        $questiondata = $generator->create_question('description', null, ['category' => $cat->id]);
        $question = \question_bank::load_question($questiondata->id);

        $event = \core\event\question_viewed::create_from_question_instance($question, \context::instance_by_id($cat->contextid));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_viewed', $event);
        $this->assertEquals($question->id, $event->objectid);
        $this->assertEquals($cat->id, $event->other['categoryid']);
        $this->assertDebuggingNotCalled();

    }
}
