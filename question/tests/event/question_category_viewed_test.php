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

namespace core_question\event;

use context_module;

/**
 * Unit tests for question_category_viewed
 *
 * @package   core_question
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\event\question_category_viewed
 */
final class question_category_viewed_test extends \advanced_testcase {
    /**
     * Test creating and triggering an event from a category instance.
     *
     * @covers ::create_from_question_category_instance
     */
    public function test_create_from_question_category_instance(): void {
        $this->resetAfterTest();
        // Create the category.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->get_plugin_generator('mod_quiz')->create_instance(['course' => $course->id]);
        $context = context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        // Create the category.
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'New category',
            'info' => 'Description',
            'idnumber' => 'newcategory',
        ]);

        // Log the view of this category.
        $event = \core\event\question_category_viewed::create_from_question_category_instance($category, $context);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_category_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($category->id, $event->objectid);
        $this->assertDebuggingNotCalled();
    }
}
