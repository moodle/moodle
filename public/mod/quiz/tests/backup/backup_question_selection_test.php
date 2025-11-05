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

namespace mod_quiz\backup;

defined('MOODLE_INTERNAL') || die();

use mod_quiz\quiz_settings;
use mod_quiz\structure;

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Unit tests ensuring only required questions are included in backups.
 *
 * @package   mod_quiz
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \backup_questions_structure_step
 * @covers \backup_question_dbops
 * @covers \backup_quiz_activity_structure_step
 */
final class backup_question_selection_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;

    /**
     * Set up data to back up.
     *
     * A course contains a quiz and a qbank, and a second course contains a shared qbank.
     * Each of these contains a some categories and some questions.
     * The quiz uses 2 questions from its own question bank, plus 1 from the course qbank, 1 from the shared qbank,
     * and a random question selecting questions from a separate category in the shared qbank.
     * A user manages both courses.
     *
     * @return array The manager, quiz, questions and course records.
     */
    protected function create_quiz_and_questions() {
        $manager = $this->getDataGenerator()->create_user();
        $this->setUser($manager);
        $course = $this->getDataGenerator()->create_course();
        $sharedcourse = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($manager->id, $course->id, 'manager');
        $this->getDataGenerator()->enrol_user($manager->id, $sharedcourse->id, 'manager');
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        // Create some question banks and a quiz with 2 categories each.

        $courseqbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $coursequestions = $questiongenerator->create_categories_and_questions(
            \context_module::instance($courseqbank->cmid),
            [
                'courseparentcat' => [
                    'courseq1' => 'shortanswer',
                    'courseq2' => 'shortanswer',
                    'coursechildcat' => [
                        'courseq3' => 'shortanswer',
                        'courseq4' => 'shortanswer',
                    ],
                ],
            ]
        );
        $sharedqbank = self::getDataGenerator()->create_module('qbank', ['course' => $sharedcourse->id]);
        $sharedquestions = $questiongenerator->create_categories_and_questions(
            \context_module::instance($sharedqbank->cmid),
            [
                'sharedparentcat' => [
                    'sharedq1' => 'shortanswer',
                    'sharedq2' => 'shortanswer',
                    'sharedchildcat' => [
                        'sharedq3' => 'shortanswer',
                        'sharedq4' => 'shortanswer',
                    ],
                ],
                'tagcat1' => [
                    'tagq1' => 'shortanswer',
                    'tagq2' => 'shortanswer',
                    'tagq3' => 'shortanswer',
                ],
                'tagcat2' => [
                    'tagq4' => 'shortanswer',
                    'tagq5' => 'shortanswer',
                    'tagq6' => 'shortanswer',
                ],
            ]
        );
        $quiz = $this->create_test_quiz($course);
        $quizquestions = $questiongenerator->create_categories_and_questions(
            \context_module::instance($quiz->cmid),
            [
                'quizparentcat' => [
                    'quizq1' => 'shortanswer',
                    'quizq2' => 'shortanswer',
                    'quizchildcat' => [
                        'quizq3' => 'shortanswer',
                        'quizq4' => 'shortanswer',
                    ],
                ],

            ]
        );

        $questiongenerator->create_question_tag(['questionid' => $sharedquestions['tagcat1']['tagq1']->id, 'tag' => 'mytag']);
        $questiongenerator->create_question_tag(['questionid' => $sharedquestions['tagcat1']['tagq2']->id, 'tag' => 'mytag']);
        $questiongenerator->create_question_tag(['questionid' => $sharedquestions['tagcat2']['tagq5']->id, 'tag' => 'mytag']);

        $tags = \core_tag_tag::get_item_tags('core_question', 'question', $sharedquestions['tagcat1']['tagq1']->id);
        $mytag = reset($tags);

        // Add a question from the shared bank child category.
        quiz_add_quiz_question($sharedquestions['sharedparentcat']['sharedchildcat']['sharedq3']->id, $quiz);
        // Add a question from the course bank parent category.
        quiz_add_quiz_question($coursequestions['courseparentcat']['courseq2']->id, $quiz);
        // Add a question from the quiz bank categories.
        quiz_add_quiz_question($quizquestions['quizparentcat']['quizq1']->id, $quiz);
        // Add random question to select tagged questions from 2 different categories.
        $settings = quiz_settings::create($quiz->id);
        $structure = structure::create_for_quiz($settings);
        $structure->add_random_questions(1, 1, [
            'filter' => [
                'category' => [
                    'jointype' => \core\output\datafilter::JOINTYPE_ANY,
                    'values' => [$sharedquestions['tagcat1']['tagq1']->category],
                    'filteroptions' => ['includesubcategories' => false],
                ],
                'qtagids' => [
                    'jointype' => \core\output\datafilter::JOINTYPE_ANY,
                    'values' => [$mytag->id],
                ],
            ],
        ]);
        $structure->add_random_questions(1, 1, [
            'filter' => [
                'category' => [
                    'jointype' => \core\output\datafilter::JOINTYPE_ANY,
                    'values' => [$sharedquestions['tagcat2']['tagq4']->category],
                    'filteroptions' => ['includesubcategories' => false],
                ],
                'qtagids' => [
                    'jointype' => \core\output\datafilter::JOINTYPE_ANY,
                    'values' => [$mytag->id],
                ],
            ],
        ]);

        return [
            $manager,
            $quiz,
            $quizquestions,
            $coursequestions,
            $sharedquestions,
            $course,
        ];
    }

    /**
     * Test that backing up a quiz only includes the questions owned or used by the quiz.
     */
    public function test_quiz_backup_excludes_unused_questions(): void {
        global $DB;
        $this->resetAfterTest();

        [
            $manager,
            $quiz,
            $quizquestions,
            $coursequestions,
            $sharedquestions,
        ] = $this->create_quiz_and_questions();

        // Backup the quiz.
        $bc = new \backup_controller(
            \backup::TYPE_1ACTIVITY,
            $quiz->cmid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $manager->id,
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        $course2 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($manager->id, $course2->id, 'manager');
        $rc = new \restore_controller($backupid, $course2->id, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
            $manager->id, \backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $backupquestions = $DB->get_records_menu('backup_ids_temp', ['itemname' => 'question'], '', 'id, itemid');
        // Backup should contain used questions from shared qbanks.
        $this->assertContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq3']->id, $backupquestions);
        $this->assertContains((string) $coursequestions['courseparentcat']['courseq2']->id, $backupquestions);
        // Backup should contain all questions from quiz's bank.
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq1']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq2']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq3']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq4']->id, $backupquestions);
        // Backup should contain questions matched by random question filters.
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq1']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq2']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat2']['tagq5']->id, $backupquestions);
        // All other questions should be excluded.
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq1']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq2']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq4']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['courseq1']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq3']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat1']['tagq3']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq6']->id, $backupquestions);
        $this->assertCount(9, $backupquestions);
        // Clean up.
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Test that backing up a quiz only includes the questions owned or used by the quiz
     * when the quiz has legacy JSON values in the question_set_references table.
     */
    public function test_quiz_backup_with_legacy_reference_filter_condition(): void {
        global $DB;
        $this->resetAfterTest();

        [
            $manager,
            $quiz,
            $quizquestions,
            $coursequestions,
            $sharedquestions,
        ] = $this->create_quiz_and_questions();

        // Revert the filtercondition to the legacy JSON format.
        $questionsetreference = $DB->get_record('question_set_references', []);
        $filtercondition = json_decode($questionsetreference->filtercondition);
        $tag = \core_tag_tag::get($filtercondition->filter->qtagids->values[0]);
        $questionsetreference->filtercondition = json_encode([
            'questioncategoryid' => $filtercondition->filter->category->values[0],
            'includingsubcategories' => $filtercondition->filter->category->filteroptions->includesubcategories,
            'tags' => ["$tag->id,$tag->name"],
        ]);
        $DB->update_record('question_set_references', $questionsetreference);

        // Backup the quiz.
        $bc = new \backup_controller(
            \backup::TYPE_1ACTIVITY,
            $quiz->cmid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $manager->id,
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        $course2 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($manager->id, $course2->id, 'manager');
        $rc = new \restore_controller(
            $backupid,
            $course2->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $manager->id,
            \backup::TARGET_CURRENT_ADDING
        );
        $rc->execute_precheck();
        $backupquestions = $DB->get_records_menu('backup_ids_temp', ['itemname' => 'question'], '', 'id, itemid');
        // Backup should contain used questions from shared qbanks.
        $this->assertContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq3']->id, $backupquestions);
        $this->assertContains((string) $coursequestions['courseparentcat']['courseq2']->id, $backupquestions);
        // Backup should contain all questions from quiz's bank.
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq1']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq2']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq3']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq4']->id, $backupquestions);
        // Backup should contain questions matched by random question filters.
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq1']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq2']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat2']['tagq5']->id, $backupquestions);
        // All other questions should be excluded.
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq1']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq2']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq4']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['courseq1']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq3']->id, $backupquestions);
        $this->assertNotContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat1']['tagq3']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq6']->id, $backupquestions);
        $this->assertCount(9, $backupquestions);
        // Clean up.
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Test that backing up a quiz only includes the questions used in or belonging to the course.
     *
     * This should include all questions in categories belonging to quizzes or qbanks on the course, plus questions from outside
     * the course used by quizzes.
     */
    public function test_course_backup_excludes_unused_questions(): void {
        global $DB;
        $this->resetAfterTest();

        [
            $manager,
            ,
            $quizquestions,
            $coursequestions,
            $sharedquestions,
            $course,
        ] = $this->create_quiz_and_questions();

        // Backup the course.
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $manager->id,
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        $course2 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($manager->id, $course2->id, 'manager');
        $rc = new \restore_controller($backupid, $course2->id, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
            $manager->id, \backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $backupquestions = $DB->get_records_menu('backup_ids_temp', ['itemname' => 'question'], '', 'id, itemid');
        // Backup should contain used questions from shared qbanks.
        $this->assertContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq3']->id, $backupquestions);
        // Backup should contain all questions from course qbanks.
        $this->assertContains((string) $coursequestions['courseparentcat']['courseq1']->id, $backupquestions);
        $this->assertContains((string) $coursequestions['courseparentcat']['courseq2']->id, $backupquestions);
        $this->assertContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq3']->id, $backupquestions);
        $this->assertContains((string) $coursequestions['courseparentcat']['coursechildcat']['courseq4']->id, $backupquestions);
        // Backup should contain all questions from quiz's bank.
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq1']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizq2']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq3']->id, $backupquestions);
        $this->assertContains((string) $quizquestions['quizparentcat']['quizchildcat']['quizq4']->id, $backupquestions);
        // Backup should contain questions matched by random question filters.
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq1']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat1']['tagq2']->id, $backupquestions);
        $this->assertContains((string) $sharedquestions['tagcat2']['tagq5']->id, $backupquestions);
        // All other questions should be excluded.
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq1']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedq2']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['sharedparentcat']['sharedchildcat']['sharedq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat1']['tagq3']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq4']->id, $backupquestions);
        $this->assertNotContains((string) $sharedquestions['tagcat2']['tagq6']->id, $backupquestions);
        $this->assertCount(12, $backupquestions);
        // Clean up.
        $rc->execute_plan();
        $rc->destroy();
    }
}

