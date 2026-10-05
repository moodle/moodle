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

use advanced_testcase;
use core\context\course;
use core\context\module;
use core\di;
use moodle_database;

/**
 * Unit tests for \core_question\local\bank\question_counts
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_question\local\bank\question_counts::class)]
final class question_counts_test extends advanced_testcase {
    /**
     * An empty bank should return a count of 0.
     */
    public function test_by_course_modules_empty(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 0], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * A bank should return the correct number of questions.
     */
    public function test_by_course_modules_questions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 2], $counts->by_course_modules([$qbankcontext->id]));

        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);

        $this->assertEquals([$qbank->cmid => 3], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * A question with multiple versions should only be counted once.
     */
    public function test_by_course_modules_versions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $q1 = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->update_question($q1, overrides: ['questiontext' => 'edited']);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 2], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * Subquestions should not be included in the question bank's total
     */
    public function test_by_course_modules_subquestions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('multianswer', 'twosubq', ['category' => $category->id]);

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 1], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * Hidden questions should not be included in the question bank's total
     */
    public function test_by_course_modules_hidden_questions(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $hiddenquestion = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $DB->set_field(
            'question_versions',
            'status',
            question_version_status::QUESTION_STATUS_HIDDEN,
            ['questionid' => $hiddenquestion->id],
        );

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 1], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * A question should be included in the bank's total if it has a newer version, but that version is hidden.
     */
    public function test_by_course_modules_hidden_newer_version(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $hiddenquestion = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->update_question($hiddenquestion, null, ['status' => question_version_status::QUESTION_STATUS_HIDDEN]);

        $counts = new question_counts();

        $this->assertEquals([$qbank->cmid => 2], $counts->by_course_modules([$qbankcontext->id]));
    }

    /**
     * All course modules using the question bank should have their count returned.
     */
    public function test_question_count_multiple_banks(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $qbank1 = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbank2 = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbank3 = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        // Quizzes can have questions too.
        $quiz = self::getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        // Pages can't have questions. This cmid should not be in the list of counts.
        self::getDataGenerator()->create_module('page', ['course' => $course->id]);

        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        // Generate questions.
        // 1 in qbank 1.
        $qbank1context = module::instance($qbank1->cmid);
        $category1 = question_get_default_category($qbank1context->id, true);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category1->id]);
        // None in qbank 2.
        $qbank2context = module::instance($qbank2->cmid);
        // 3 in qbank 3.
        $qbank3context = module::instance($qbank3->cmid);
        $category3 = question_get_default_category($qbank3context->id, true);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category3->id]);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category3->id]);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category3->id]);
        // 2 in the quiz.
        $quizcontext = module::instance($quiz->cmid);
        $category4 = question_get_default_category($quizcontext->id, true);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category4->id]);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category4->id]);

        $counts = new question_counts();

        $this->assertEquals(
            [
                $qbank1->cmid => 1,
                $qbank2->cmid => 0,
                $qbank3->cmid => 3,
                $quiz->cmid => 2,
            ],
            $counts->by_course_modules([$qbank1context->id, $qbank2context->id, $qbank3context->id, $quizcontext->id]),
        );
    }

    /**
     * An empty category should return a count of 0.
     */
    public function test_by_category_empty(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $generator = self::getDataGenerator();
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            0,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }

    /**
     * An category with questions should return the correct count.
     */
    public function test_by_category_question(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $generator = self::getDataGenerator();
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            2,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );

        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);

        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            3,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }

    /**
     * A question with multiple versions should only be counted once, unless specified.
     */
    public function test_by_category_versions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        // Create 1 question with 2 versions, and another with 1 version.
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $q1 = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->update_question($q1, overrides: ['questiontext' => 'edited']);
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        // Only count latest versions.
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            2,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );

        // Count all versions.
        [$sql, $params] = $counts->by_category_query(showallversions: 1, categoryparam: ':categoryid');

        $this->assertEquals(
            3,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }

    /**
     * Subquestions should not be counted.
     */
    public function test_by_category_subquestions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('multianswer', 'twosubq', ['category' => $category->id]);
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            1,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }

    /**
     * Hidden questions should not be included in the question bank's total
     */
    public function test_by_category_hidden_questions(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $hiddenquestion = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $DB->set_field(
            'question_versions',
            'status',
            question_version_status::QUESTION_STATUS_HIDDEN,
            ['questionid' => $hiddenquestion->id],
        );
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            1,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }

    /**
     * A question with a newer version that's hidden should still be counted.
     */
    public function test_by_category_hidden_newer_version(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $db = di::get(moodle_database::class);
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $bankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($bankcontext->id, true);
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $hiddenquestion = $questiongenerator->create_question('truefalse', overrides: ['category' => $category->id]);
        $questiongenerator->update_question($hiddenquestion, null, ['status' => question_version_status::QUESTION_STATUS_HIDDEN]);
        $questiongenerator->create_categories_and_questions($bankcontext, ['category2' => ['q1' => 'truefalse']]);

        $counts = new question_counts();
        [$sql, $params] = $counts->by_category_query(categoryparam: ':categoryid');

        $this->assertEquals(
            2,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );

        // The hidden version should never be counted.
        [$sql, $params] = $counts->by_category_query(showallversions: 1, categoryparam: ':categoryid');

        $this->assertEquals(
            2,
            $db->get_field_sql(
                $sql,
                [
                    ...$params,
                    'categoryid' => $category->id,
                ]
            )
        );
    }
}
