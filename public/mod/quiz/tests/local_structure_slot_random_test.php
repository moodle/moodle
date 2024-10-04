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

namespace mod_quiz;

defined('MOODLE_INTERNAL') || die();

use mod_quiz\question\bank\qbank_helper;

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Class mod_quiz_local_structure_slot_random_test
 * Class for tests related to the {@link \mod_quiz\local\structure\slot_random} class.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_quiz\local\structure\slot_random
 */
final class local_structure_slot_random_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    /**
     * Constructor test.
     */
    public function test_constructor(): void {
        global $SITE;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        // Create a question category in the system context.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();

        // Create a random question without adding it to a quiz.
        // We don't want to use quiz_add_random_questions because that itself, instantiates an object from the slot_random class.
        $form = new \stdClass();
        $form->category = $category->id . ',' . $category->contextid;
        $form->includesubcategories = true;
        $form->fromtags = [];
        $form->defaultmark = 1;
        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_HIDDEN;
        $form->stamp = make_unique_id_code();

        // Set the filter conditions.
        $filtercondition = new \stdClass();
        $filtercondition->filters = \question_filter_test_helper::create_filters([$category->id], true);

        // Slot data.
        $randomslotdata = new \stdClass();
        $randomslotdata->quizid = $quiz->id;
        $randomslotdata->maxmark = 1;
        $randomslotdata->usingcontextid = \context_module::instance($quiz->cmid)->id;
        $randomslotdata->questionscontextid = $category->contextid;

        // Insert the random question to the quiz.
        $randomslot = new \mod_quiz\local\structure\slot_random($randomslotdata);
        $randomslot->set_filter_condition(json_encode($filtercondition));

        $rc = new \ReflectionClass('\mod_quiz\local\structure\slot_random');
        $rcp = $rc->getProperty('filtercondition');
        $record = json_decode($rcp->getValue($randomslot));

        $this->assertEquals($quiz->id, $randomslot->get_quiz()->id);
        $this->assertEquals($category->id, $record->filters->category->values[0]);
        $this->assertTrue($record->filters->category->filteroptions->includesubcategories);

        $rcp = $rc->getProperty('record');
        $record = $rcp->getValue($randomslot);
        $this->assertEquals(1, $record->maxmark);
    }

    public function test_get_quiz_quiz(): void {
        global $SITE, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        // Create a question category in the system context.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();

        $this->add_random_questions($quiz->id, 0, $category->id, 1);

        // Set the filter conditions.
        $filtercondition = new \stdClass();
        $filtercondition->filters = \question_filter_test_helper::create_filters([$category->id], 1);

        // Slot data.
        $randomslotdata = new \stdClass();
        $randomslotdata->quizid = $quiz->id;
        $randomslotdata->maxmark = 1;
        $randomslotdata->usingcontextid = \context_module::instance($quiz->cmid)->id;
        $randomslotdata->questionscontextid = $category->contextid;

        $randomslot = new \mod_quiz\local\structure\slot_random($randomslotdata);
        $randomslot->set_filter_condition(json_encode($filtercondition));

        // The create_instance had injected an additional cmid propery to the quiz. Let's remove that.
        unset($quiz->cmid);

        $this->assertEquals($quiz, $randomslot->get_quiz());
    }

    public function test_set_quiz(): void {
        global $SITE, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        // Create a question category in the system context.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();

        $this->add_random_questions($quiz->id, 0, $category->id, 1);

        // Set the filter conditions.
        $filtercondition = new \stdClass();
        $filtercondition->filters = \question_filter_test_helper::create_filters([$category->id], 1);

        // Slot data.
        $randomslotdata = new \stdClass();
        $randomslotdata->quizid = $quiz->id;
        $randomslotdata->maxmark = 1;
        $randomslotdata->usingcontextid = \context_module::instance($quiz->cmid)->id;
        $randomslotdata->questionscontextid = $category->contextid;

        $randomslot = new \mod_quiz\local\structure\slot_random($randomslotdata);
        $randomslot->set_filter_condition(json_encode($filtercondition));

        // The create_instance had injected an additional cmid propery to the quiz. Let's remove that.
        unset($quiz->cmid);

        $randomslot->set_quiz($quiz);

        $rc = new \ReflectionClass('\mod_quiz\local\structure\slot_random');
        $rcp = $rc->getProperty('quiz');
        $quizpropery = $rcp->getValue($randomslot);

        $this->assertEquals($quiz, $quizpropery);
    }

    private function setup_for_test_tags($tagnames) {
        global $SITE, $DB;

        // Create a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        // Create a question category in the system context.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();

        $this->add_random_questions($quiz->id, 0, $category->id, 1);

        // Slot data.
        $randomslotdata = new \stdClass();
        $randomslotdata->quizid = $quiz->id;
        $randomslotdata->maxmark = 1;
        $randomslotdata->usingcontextid = \context_module::instance($quiz->cmid)->id;
        $randomslotdata->questionscontextid = $category->contextid;

        $randomslot = new \mod_quiz\local\structure\slot_random($randomslotdata);

        // Create tags.
        foreach ($tagnames as $tagname) {
            $tagrecord = [
                'isstandard' => 1,
                'flag' => 0,
                'rawname' => $tagname,
                'description' => $tagname . ' desc'
            ];
            $tags[$tagname] = $this->getDataGenerator()->create_tag($tagrecord);
        }

        return [$randomslot, $tags];
    }

    public function test_set_tags_filter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        list($randomslot, $tags) = $this->setup_for_test_tags(['foo', 'bar']);

        $qtagids = [$tags['foo']->id, $tags['bar']->id];
        $filtercondition = new \stdClass();
        $filtercondition->filters = \question_filter_test_helper::create_filters([], 0, $qtagids);
        $randomslot->set_filter_condition(json_encode($filtercondition));

        $rc = new \ReflectionClass('\mod_quiz\local\structure\slot_random');
        $rcp = $rc->getProperty('filtercondition');
        $tagspropery = $rcp->getValue($randomslot);

        $this->assertEquals([$tags['foo']->id, $tags['bar']->id],
            (array)json_decode($tagspropery)->filters->qtagids->values);
    }

    public function test_insert(): void {
        global $SITE;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);
        $quizcontext = \context_module::instance($quiz->cmid);

        // Create a question category in the system context.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();

        // Create a random question without adding it to a quiz.
        $form = new \stdClass();
        $form->category = $category->id . ',' . $category->contextid;
        $form->includesubcategories = true;
        $form->fromtags = [];
        $form->defaultmark = 1;
        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_HIDDEN;
        $form->stamp = make_unique_id_code();

        // Prepare 2 tags.
        $tagrecord = [
            'isstandard' => 1,
            'flag' => 0,
            'rawname' => 'foo',
            'description' => 'foo desc'
        ];
        $footag = $this->getDataGenerator()->create_tag($tagrecord);
        $tagrecord = [
            'isstandard' => 1,
            'flag' => 0,
            'rawname' => 'bar',
            'description' => 'bar desc'
        ];
        $bartag = $this->getDataGenerator()->create_tag($tagrecord);


        // Set the filter conditions.
        $filtercondition = new \stdClass();
        $filtercondition->filter = \question_filter_test_helper::create_filters([$category->id], true, [$footag->id, $bartag->id]);

        // Slot data.
        $randomslotdata = new \stdClass();
        $randomslotdata->quizid = $quiz->id;
        $randomslotdata->maxmark = 1;
        $randomslotdata->usingcontextid = $quizcontext->id;
        $randomslotdata->questionscontextid = $category->contextid;

        // Insert the random question to the quiz.
        $randomslot = new \mod_quiz\local\structure\slot_random($randomslotdata);
        $randomslot->set_filter_condition(json_encode($filtercondition));
        $randomslot->insert(1); // Put the question on the first page of the quiz.

        $slots = qbank_helper::get_question_structure($quiz->id, $quizcontext);
        $quizslot = reset($slots);

        $filter = $quizslot->filtercondition['filter'];

        $this->assertEquals($category->id, $filter['category']['values'][0]);
        $this->assertTrue($filter['category']['filteroptions']['includesubcategories']);
        $this->assertEquals(1, $quizslot->maxmark);

        $this->assertCount(2, $filter['qtagids']['values']);
        $this->assertEqualsCanonicalizing(
                [
                    ['tagid' => $footag->id],
                    ['tagid' => $bartag->id]
                ],
                array_map(function($tagid) {
                    return ['tagid' => $tagid];
                }, $filter['qtagids']['values']));
    }
}
