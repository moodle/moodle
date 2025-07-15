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

namespace core_question\local\bank;

use context_course;
use context_coursecat;
use context_module;
use context_system;
use context_user;

/**
 * Unit tests for the context_to_string_translator class.
 *
 * @package   core_question
 * @category  test
 * @copyright 2023 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\local\bank\context_to_string_translator
 */
final class context_to_string_translator_test extends \advanced_testcase {

    public function test_context_to_string_translator_test_good_case(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Generate a quiz in a course in a category.
        $systemcontext = context_system::instance();

        $category = $generator->create_category();
        $categorycontext = context_coursecat::instance($category->id);

        $course = $generator->create_course(['category' => $category->id]);
        $coursecontext = context_course::instance($course->id);

        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $quizcontext = context_module::instance($quiz->cmid);

        // Create the context_to_string_translator.
        $translator = new context_to_string_translator([$systemcontext, $categorycontext, $coursecontext, $quizcontext]);

        // Verify its behaviour.
        $this->assertEquals('module', $translator->context_to_string($quizcontext->id));
        $this->assertEquals($quizcontext->id, $translator->string_to_context('module'));

        $this->assertEquals('course', $translator->context_to_string($coursecontext->id));
        $this->assertEquals($coursecontext->id, $translator->string_to_context('course'));

        $this->assertEquals('cat1', $translator->context_to_string($categorycontext->id));
        $this->assertEquals($categorycontext->id, $translator->string_to_context('cat1'));

        $this->assertEquals('system', $translator->context_to_string($systemcontext->id));
        $this->assertEquals($systemcontext->id, $translator->string_to_context('system'));
    }

    public function test_context_to_string_translator_throws_exception_with_bad_context(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $this->expectExceptionMessage('Unexpected context level User for context ' .
                $context->id . ' in generate_context_to_string_array. ' .
                'Questions can never exist in this type of context.');
        new context_to_string_translator((new question_edit_contexts($context))->all());
    }
}
