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

namespace core_question\external;

use core\context\module;

/**
 * Unit tests for core_question\external\search_shared_banks
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\external\search_shared_banks
 */
final class search_shared_banks_test extends \advanced_testcase {

    /**
     * Create a set of question banks across 3 courses.
     *
     * One bank belongs to a quiz, which is not searchable as it is not shared.
     * One bank does not include the word "test" in its name, so will not match that search.
     * One bank is on a course where the user has different permissions.
     * One bank is on the same course as the quiz, so will not be returned unless banks on the same course are included.
     *
     * @return array
     */
    protected function create_banks(): array {
        $generator = $this->getDataGenerator();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $qbankgenerator = $this->getDataGenerator()->get_plugin_generator('mod_qbank');

        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();
        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $course1->id, 'teacher');
        $generator->enrol_user($teacher->id, $course2->id, 'teacher');
        $generator->enrol_user($teacher->id, $course3->id, 'editingteacher');

        $quiz = $quizgenerator->create_instance(['name' => 'Test quiz', 'course' => $course1->id]);
        $qbank1 = $qbankgenerator->create_instance(['name' => 'Test qbank 1', 'course' => $course2->id]);
        $qbank2 = $qbankgenerator->create_instance(['name' => 'Test qbank 2', 'course' => $course2->id]);
        $qbank3 = $qbankgenerator->create_instance(['name' => 'Different name', 'course' => $course2->id]);
        $qbank4 = $qbankgenerator->create_instance(['name' => 'Test qbank with different permissions', 'course' => $course3->id]);

        return [
            $course1,
            $course2,
            $course3,
            $teacher,
            module::instance($quiz->cmid),
            $qbank1,
            $qbank2,
            $qbank3,
            $qbank4,
        ];
    }

    /**
     * Call the function with no search string. All banks the user has permission to should be returned.
     */
    public function test_empty_search(): void {
        $this->resetAfterTest();
        [
            ,
            $course2,
            $course3,
            $teacher,
            $quizcontext,
            $qbank1,
            $qbank2,
            $qbank3,
            $qbank4,
        ] = $this->create_banks();

        $this->setUser($teacher);
        $result = search_shared_banks::execute($quizcontext->id);

        $this->assertEquals(
            [
                [
                    'label' => "{$course2->shortname} - {$qbank1->name}",
                    'value' => $qbank1->cmid,
                ],
                [
                    'label' => "{$course2->shortname} - {$qbank2->name}",
                    'value' => $qbank2->cmid,
                ],
                [
                    'label' => "{$course2->shortname} - {$qbank3->name}",
                    'value' => $qbank3->cmid,
                ],
                [
                    'label' => "{$course3->shortname} - {$qbank4->name}",
                    'value' => $qbank4->cmid,
                ],
            ],
            $result['sharedbanks'],
        );
    }

    /**
     * Call the function with a search string matching a subset of available banks. Only those matching should be returned.
     */
    public function test_search(): void {
        $this->resetAfterTest();
        [
            ,
            $course2,
            $course3,
            $teacher,
            $quizcontext,
            $qbank1,
            $qbank2,
            ,
            $qbank4,
        ] = $this->create_banks();

        $this->setUser($teacher);
        $result = search_shared_banks::execute($quizcontext->id, 'Test');

        $this->assertEquals(
            [
                [
                    'label' => "{$course2->shortname} - {$qbank1->name}",
                    'value' => $qbank1->cmid,
                ],
                [
                    'label' => "{$course2->shortname} - {$qbank2->name}",
                    'value' => $qbank2->cmid,
                ],
                [
                    'label' => "{$course3->shortname} - {$qbank4->name}",
                    'value' => $qbank4->cmid,
                ],
            ],
            $result['sharedbanks'],
        );
    }

    /**
     * Call the function with a different capability. Only results where the user has that capability should be returned.
     */
    public function test_search_different_capability(): void {
        $this->resetAfterTest();
        [
            ,
            ,
            $course3,
            $teacher,
            $quizcontext,
            ,
            ,
            ,
            $qbank4,
        ] = $this->create_banks();

        $this->setUser($teacher);
        $result = search_shared_banks::execute($quizcontext->id, 'Test', ['edit']);

        $this->assertEquals(
            [
                [
                    'label' => "{$course3->shortname} - {$qbank4->name}",
                    'value' => $qbank4->cmid,
                ],
            ],
            $result['sharedbanks'],
        );
    }

    /**
     * If there are more than the max number of results, a placeholder is returned at the end.
     */
    public function test_search_max_results(): void {
        $this->resetAfterTest();
        [
            ,
            $course2,
            ,
            $teacher,
            $quizcontext,
        ] = $this->create_banks();

        $qbankgenerator = $this->getDataGenerator()->get_plugin_generator('mod_qbank');
        for ($i = 1; $i <= search_shared_banks::MAX_RESULTS + 2; $i++) {
            $qbankgenerator->create_instance(['name' => "Extra qbank {$i}", 'course' => $course2->id]);
        }

        $this->setUser($teacher);
        $result = search_shared_banks::execute($quizcontext->id, 'Extra');
        $this->assertCount(search_shared_banks::MAX_RESULTS + 1, $result['sharedbanks']);
        $lastresult = end($result['sharedbanks']);
        $this->assertEquals(
            [
                'label' => get_string('otherquestionbankstoomany', 'question', search_shared_banks::MAX_RESULTS),
                'value' => 0,
            ],
            $lastresult
        );
    }
}
