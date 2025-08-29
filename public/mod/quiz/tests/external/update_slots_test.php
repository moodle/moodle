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

namespace mod_quiz\external;

use core_question_generator;
use mod_quiz\quiz_settings;
use required_capability_exception;

/**
 * Test for the update_slots service.
 *
 * @package   mod_quiz
 * @category  external
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\external\update_slots
 */
final class update_slots_test extends \core_external\tests\externallib_testcase {
    public function test_update_slots_service_works(): void {
        global $DB;

        $quizobj = $this->create_quiz_with_two_shortanswer_questions();
        $this->setAdminUser();
        $structure = $quizobj->get_structure();

        // No changes to slot 1.
        $slot1data = [
            'id' => $structure->get_slot_by_number(1)->id,
        ];
        // Change everything in slot 2.
        $slot2data = [
            'id' => $structure->get_slot_by_number(2)->id,
            'displaynumber' => '1b',
            'requireprevious' => true,
            'maxmark' => 7,
            'quizgradeitemid' => 123,
        ];

        update_slots::execute($quizobj->get_quizid(), [$slot1data, $slot2data]);

        $slot = $DB->get_record('quiz_slots', ['id' => $slot2data['id']]);
        $this->assertEquals('1b', $slot->displaynumber);
        $this->assertTrue((bool) $slot->requireprevious);
        $this->assertEquals(7, $slot->maxmark);
        $this->assertEquals(123, $slot->quizgradeitemid);
    }

    public function test_update_slots_checks_permissions(): void {
        $quizobj = $this->create_quiz_with_two_shortanswer_questions();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $this->expectException(required_capability_exception::class);
        update_slots::execute($quizobj->get_quizid(), []);
    }

    /**
     * Create a quiz of two shortanswer questions.
     *
     * @return quiz_settings the newly created quiz.
     */
    protected function create_quiz_with_two_shortanswer_questions(): quiz_settings {
        global $SITE;
        $this->resetAfterTest();

        // Make a quiz.
        $timeclose = time() + HOURSECS;
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance([
            'course' => $SITE->id,
            'timeclose' => $timeclose,
            'overduehandling' => 'autoabandon',
        ]);

        // Create a question.
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $saq1 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $saq2 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Add them to the quiz.
        $quizobj = quiz_settings::create($quiz->id);
        quiz_add_quiz_question($saq1->id, $quiz, 0, 1);
        quiz_add_quiz_question($saq2->id, $quiz, 0, 1);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();

        return $quizobj;
    }
}
